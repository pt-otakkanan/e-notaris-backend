<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BlogController extends Controller
{
    // GET /blogs/all/blog  (?min=true -> id,title,image saja)
    public function all(Request $request)
    {
        $min     = (bool) $request->query('min', false);
        $columns = $min ? ['id', 'title', 'image'] : ['*'];

        $blogs = Blog::orderBy('created_at', 'desc')->get($columns);

        return response()->json([
            'success' => true,
            'message' => 'Daftar blog berhasil diambil',
            'data'    => $blogs,
            'meta'    => ['count' => $blogs->count()],
        ], 200);
    }

    // GET /blogs  (?search= / ?q=, ?per_page=, ?user_id=)
    public function index(Request $request)
    {
        $q       = $request->query('search', $request->query('q'));
        $perPage = max((int) $request->query('per_page', 10), 1);
        $userId  = $request->query('user_id');

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

    // GET /blogs/{id}
    public function show($id)
    {
        $blog = Blog::find($id);

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
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'image'       => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'title.required'       => 'Judul blog wajib diisi.',
            'description.required' => 'Deskripsi blog wajib diisi.',
            'image.mimes'          => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max'            => 'Ukuran gambar maksimal 5MB.',
        ]);

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
            $folder   = 'enotaris/blogs';
            $filename = 'blog_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $request->file('image')->getRealPath(),
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'image',
                ]
            );

            $imageUrl  = $upload->getSecurePath();
            $imagePath = $publicId;
        }

        $blog = Blog::create([
            'user_id'     => $user->id,
            'title'       => $data['title'],
            'description' => $data['description'],
            'image'       => $imageUrl,
            'image_path'  => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dibuat',
            'data'    => $blog,
        ], 201);
    }

    // POST /blogs/update/{id}  (image optional, clear_image optional)
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

        $validasi = Validator::make($request->all(), [
            'title'        => ['sometimes', 'required', 'string', 'max:200'],
            'description'  => ['sometimes', 'required', 'string'],
            'image'        => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'clear_image'  => ['sometimes', 'boolean'],
        ], [
            'title.required'       => 'Judul blog wajib diisi.',
            'description.required' => 'Deskripsi blog wajib diisi.',
            'image.mimes'          => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max'            => 'Ukuran gambar maksimal 5MB.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

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

            $folder   = 'enotaris/blogs';
            $filename = 'blog_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $request->file('image')->getRealPath(),
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'image',
                ]
            );

            $blog->image      = $upload->getSecurePath();
            $blog->image_path = $publicId;
        }

        $blog->save();

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil diperbarui',
            'data'    => $blog,
        ], 200);
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

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog berhasil dihapus',
            'data'    => null,
        ], 200);
    }
}
