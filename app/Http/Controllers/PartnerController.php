<?php
// app/Http/Controllers/PartnerController.php
namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PartnerController extends Controller
{
    // GET /partners/all/partner  (?min=true -> id,name,image saja)
    public function all(Request $request)
    {
        $min     = (bool) $request->query('min', false);
        $columns = $min ? ['id', 'name', 'image'] : ['*'];

        $partners = Partner::select($columns)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar partner berhasil diambil',
            'data'    => $partners,
            'meta'    => ['count' => $partners->count()],
        ], 200);
    }

    // GET /partners (?search=/q=, ?per_page=)
    public function index(Request $request)
    {
        $q       = $request->query('search', $request->query('q'));
        $perPage = max((int) $request->query('per_page', 10), 1);

        $query = Partner::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('link', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar partner berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ],
        ], 200);
    }

    // GET /partners/{id}
    public function show($id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail partner berhasil diambil',
            'data'    => $partner,
        ], 200);
    }

    // POST /partners (multipart/form-data; image opsional)
    // body: name, link?, image?
    public function store(Request $request)
    {
        $validasi = Validator::make(
            $request->all(),
            [
                'name'  => ['required', 'string', 'max:150'],
                'link'  => ['nullable', 'url', 'max:2048'],
                'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ],
            [
                'name.required' => 'Nama partner wajib diisi.',
                'name.max'      => 'Nama partner maksimal 150 karakter.',
                'link.url'      => 'Link partner harus berupa URL yang valid.',
                'image.mimes'   => 'Format gambar harus jpg, jpeg, png, atau webp.',
                'image.max'     => 'Ukuran gambar maksimal 5MB.',
            ],
            [
                'name'  => 'nama',
                'link'  => 'tautan',
                'image' => 'gambar',
            ]
        );

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data      = $validasi->validated();
        $imageUrl  = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            [$imageUrl, $imagePath] = $this->uploadToCloudinary($request->file('image'));
        }

        return DB::transaction(function () use ($data, $imageUrl, $imagePath) {
            $partner = Partner::create([
                'name'       => $data['name'],
                'link'       => $data['link'] ?? null,
                'image'      => $imageUrl,
                'image_path' => $imagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Partner berhasil dibuat',
                'data'    => $partner,
            ], 201);
        });
    }

    // POST /partners/update/{id} (image opsional, clear_image opsional)
    public function update(Request $request, $id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $validasi = Validator::make(
            $request->all(),
            [
                'name'        => ['sometimes', 'required', 'string', 'max:150'],
                'link'        => ['sometimes', 'nullable', 'url', 'max:2048'],
                'image'       => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'clear_image' => ['sometimes', 'boolean'],
            ],
            [
                'name.required' => 'Nama partner wajib diisi.',
                'name.max'      => 'Nama partner maksimal 150 karakter.',
                'link.url'      => 'Link partner harus berupa URL yang valid.',
                'image.mimes'   => 'Format gambar harus jpg, jpeg, png, atau webp.',
                'image.max'     => 'Ukuran gambar maksimal 5MB.',
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

        return DB::transaction(function () use ($request, $partner, $data) {
            // Update field sederhana
            foreach (['name', 'link'] as $f) {
                if (array_key_exists($f, $data)) {
                    $partner->{$f} = $data[$f];
                }
            }

            // Hapus gambar jika diminta
            if (($data['clear_image'] ?? false) === true) {
                if (!empty($partner->image_path)) {
                    Cloudinary::destroy($partner->image_path);
                }
                $partner->image = null;
                $partner->image_path = null;
            }

            // Ganti gambar jika upload baru ada
            if ($request->hasFile('image')) {
                if (!empty($partner->image_path)) {
                    Cloudinary::destroy($partner->image_path);
                }
                [$imageUrl, $imagePath] = $this->uploadToCloudinary($request->file('image'));
                $partner->image      = $imageUrl;
                $partner->image_path = $imagePath;
            }

            $partner->save();

            return response()->json([
                'success' => true,
                'message' => 'Partner berhasil diperbarui',
                'data'    => $partner,
            ], 200);
        });
    }

    // DELETE /partners/{id}
    public function destroy($id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if (!empty($partner->image_path)) {
            Cloudinary::destroy($partner->image_path);
        }

        $partner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Partner berhasil dihapus',
            'data'    => null,
        ], 200);
    }

    // ===== helper private =====
    private function uploadToCloudinary($uploadedFile): array
    {
        $folder   = 'enotaris/partners';
        $filename = 'partner_' . now()->format('YmdHis');
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
}
