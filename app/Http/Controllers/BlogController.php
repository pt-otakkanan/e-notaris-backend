<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BlogController extends Controller
{
    // GET /blogs/all/blog  (?min=true -> id,title,image saja) (&with=categories)
    public function all(Request $request)
    {
        $min      = (bool) $request->query('min', false);
        $withCats = $this->shouldWithCategories($request);

        $columns = $min ? ['id', 'title', 'image'] : ['*'];

        $query = Blog::select($columns)->orderBy('created_at', 'desc');

        if ($withCats) {
            $query->with(['categories:id,name']);
        }

        $blogs = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar blog berhasil diambil',
            'data'    => $blogs,
            'meta'    => ['count' => $blogs->count()],
        ], 200);
    }

    // GET /blogs  (?search=/q=, ?per_page=, ?user_id=, ?category_blog_id=, ?with=categories)
    public function index(Request $request)
    {
        $q         = $request->query('search', $request->query('q'));
        $perPage   = max((int) $request->query('per_page', 10), 1);
        $userId    = $request->query('user_id');
        $catId     = $request->query('category_blog_id');
        $withCats  = $this->shouldWithCategories($request);

        $query = Blog::query();

        if ($userId) {
            $query->where('user_id', (int) $userId);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($catId) {
            $query->whereHas('categories', function ($q2) use ($catId) {
                $q2->where('category_blogs.id', (int) $catId);
            });
        }

        if ($withCats) {
            $query->with(['categories:id,name']);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar blog berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ],
        ], 200);
    }

    // GET /blogs/{id} (?with=categories)
    public function show($id, Request $request)
    {
        $withCats = $this->shouldWithCategories($request);

        $query = Blog::query();
        if ($withCats) {
            $query->with(['categories:id,name']);
        }

        $blog = $query->find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail blog berhasil diambil',
            'data'    => $blog,
        ], 200);
    }

    // POST /blogs  (multipart/form-data; image optional)
    // body: title, description, image?, category_blog_ids? (array<int>)
    public function store(Request $request)
    {
        $validasi = Validator::make(
            $request->all(),
            [
                'title'              => ['required', 'string', 'max:200'],
                'description'        => ['required', 'string'],
                'image'              => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'category_blog_ids'   => ['sometimes', 'array'],
                'category_blog_ids.*' => ['integer', 'exists:category_blogs,id'],
            ],
            [
                'title.required'         => 'Judul blog wajib diisi.',
                'title.max'              => 'Judul maksimal 200 karakter.',
                'description.required'   => 'Deskripsi blog wajib diisi.',

                'image.file'             => 'File gambar tidak valid.',
                'image.mimes'            => 'Format gambar harus jpg, jpeg, png, atau webp.',
                'image.max'              => 'Ukuran gambar maksimal 5MB.',

                'category_blog_ids.array'   => 'Format kategori tidak valid.',
                'category_blog_ids.*.exists' => 'Kategori tidak ditemukan.',
            ],
            // custom attribute (optional)
            [
                'title'       => 'judul',
                'description' => 'deskripsi',
                'image'       => 'gambar',
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();
        $user = $request->user();

        $imageUrl  = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            [$imageUrl, $imagePath] = $this->uploadToCloudinary($request->file('image'));
        }

        return DB::transaction(function () use ($user, $data, $imageUrl, $imagePath, $request) {
            $blog = Blog::create([
                'user_id'     => $user->id,
                'title'       => $data['title'],
                'description' => $data['description'],
                'image'       => $imageUrl,
                'image_path'  => $imagePath,
            ]);

            // Sync kategori jika ada
            if (array_key_exists('category_blog_ids', $data)) {
                $catIds = collect($data['category_blog_ids'] ?? [])
                    ->filter(fn($v) => is_numeric($v))
                    ->map(fn($v) => (int) $v)
                    ->unique()
                    ->values()
                    ->all();

                $blog->categories()->sync($catIds);
            }

            // eager categories jika diminta
            if ($this->shouldWithCategories($request)) {
                $blog->load(['categories:id,name']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Blog berhasil dibuat',
                'data'    => $blog,
            ], 201);
        });
    }

    // POST /blogs/update/{id}  (image optional, clear_image optional, category_blog_ids? array)
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $validasi = Validator::make(
            $request->all(),
            [
                'title'              => ['sometimes', 'required', 'string', 'max:200'],
                'description'        => ['sometimes', 'required', 'string'],
                'image'              => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'clear_image'        => ['sometimes', 'boolean'],
                'category_blog_ids'   => ['sometimes', 'array'],
                'category_blog_ids.*' => ['integer', 'exists:category_blogs,id'],
            ],
            [
                'title.required'       => 'Judul blog wajib diisi.',
                'description.required' => 'Deskripsi blog wajib diisi.',
                'image.mimes'          => 'Format gambar harus jpg, jpeg, png, atau webp.',
                'image.max'            => 'Ukuran gambar maksimal 5MB.',
                'category_blog_ids.array'   => 'Format kategori tidak valid.',
                'category_blog_ids.*.exists' => 'Kategori tidak ditemukan.',
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        return DB::transaction(function () use ($request, $blog, $data) {
            // Update field sederhana
            foreach (['title', 'description'] as $f) {
                if (array_key_exists($f, $data)) {
                    $blog->{$f} = $data[$f];
                }
            }

            // Hapus gambar jika diminta
            if (($data['clear_image'] ?? false) === true) {
                if (!empty($blog->image_path)) {
                    Cloudinary::destroy($blog->image_path);
                }
                $blog->image = null;
                $blog->image_path = null;
            }

            // Ganti gambar jika upload baru ada
            if ($request->hasFile('image')) {
                if (!empty($blog->image_path)) {
                    Cloudinary::destroy($blog->image_path);
                }
                [$imageUrl, $imagePath] = $this->uploadToCloudinary($request->file('image'));
                $blog->image      = $imageUrl;
                $blog->image_path = $imagePath;
            }

            $blog->save();

            // Sync kategori hanya jika field dikirim (kalau nggak dikirim → tidak diubah)
            if (array_key_exists('category_blog_ids', $data)) {
                $catIds = collect($data['category_blog_ids'] ?? [])
                    ->filter(fn($v) => is_numeric($v))
                    ->map(fn($v) => (int) $v)
                    ->unique()
                    ->values()
                    ->all();

                $blog->categories()->sync($catIds); // kirim [] → detach semua
            }

            if ($this->shouldWithCategories($request)) {
                $blog->load(['categories:id,name']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Blog berhasil diperbarui',
                'data'    => $blog,
            ], 200);
        });
    }

    // DELETE /blogs/{id}
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if (!empty($blog->image_path)) {
            Cloudinary::destroy($blog->image_path);
        }

        // pivot akan terhapus otomatis karena cascadeOnDelete di FK pivot (disarankan)
        // atau bisa manual: $blog->categories()->detach();

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dihapus',
            'data'    => null,
        ], 200);
    }

    // ===== helper private =====

    private function uploadToCloudinary($uploadedFile): array
    {
        $folder   = 'enotaris/blogs';
        $filename = 'blog_' . now()->format('YmdHis');
        $publicId = "{$folder}/{$filename}";

        $upload = Cloudinary::upload(
            $uploadedFile->getRealPath(),
            [
                'folder'        => $folder . '/',
                'public_id'     => $filename,
                'overwrite'     => true,
                'resource_type' => 'image',
            ]
        );

        return [$upload->getSecurePath(), $publicId];
    }

    private function shouldWithCategories(Request $request): bool
    {
        // ?with=categories / ?with=categories,foo
        $with = $request->query('with');
        if (!$with) return false;
        $parts = array_map('trim', explode(',', $with));
        return in_array('categories', $parts, true);
    }
}
