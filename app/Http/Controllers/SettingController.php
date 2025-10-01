<?php
// app/Http/Controllers/SettingController.php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SettingController extends Controller
{
    /** Ambil baris singleton (buat kalau belum ada). */
    private function current(): Setting
    {
        // Kunci di id=1 agar konsisten
        return Setting::firstOrCreate(['id' => 1], []);
    }

    /** GET /settings (admin) */
    public function get()
    {
        $s = $this->current();
        return response()->json([
            'success' => true,
            'message' => 'Setting ditemukan',
            'data'    => $s,
        ], 200);
    }

    /** GET /public/settings (public) */
    public function publicGet()
    {
        $s = Setting::find(1) ?? Setting::orderBy('id')->first();
        return response()->json([
            'success' => true,
            'message' => $s ? 'Setting ditemukan' : 'Belum ada setting',
            'data'    => $s,
        ], 200);
    }

    /** POST /settings (multipart) → upsert single row */
    public function upsert(Request $request)
    {
        $valid = Validator::make(
            $request->all(),
            [
                // media
                'logo'           => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'favicon'        => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'],
                'clear_logo'     => ['sometimes', 'boolean'],
                'clear_favicon'  => ['sometimes', 'boolean'],

                // kontak & sosial
                'telepon'        => ['sometimes', 'nullable', 'string', 'max:50'],
                'facebook'       => ['sometimes', 'nullable', 'url', 'max:2048'],
                'instagram'      => ['sometimes', 'nullable', 'url', 'max:2048'],
                'twitter'        => ['sometimes', 'nullable', 'url', 'max:2048'],
                'linkedin'       => ['sometimes', 'nullable', 'url', 'max:2048'],

                // konten
                'title_hero'     => ['sometimes', 'nullable', 'string', 'max:200'],
                'desc_hero'      => ['sometimes', 'nullable', 'string'],
                'desc_footer'    => ['sometimes', 'nullable', 'string'],
            ],
            [
                'logo.mimes'     => 'Logo harus jpg, jpeg, png, atau webp.',
                'logo.max'       => 'Ukuran logo maksimal 5MB.',
                'favicon.mimes'  => 'Favicon harus jpg, jpeg, png, webp, atau ico.',
                'favicon.max'    => 'Ukuran favicon maksimal 2MB.',
                'facebook.url'   => 'URL Facebook tidak valid.',
                'instagram.url'  => 'URL Instagram tidak valid.',
                'twitter.url'    => 'URL Twitter tidak valid.',
                'linkedin.url'   => 'URL LinkedIn tidak valid.',
            ]
        );

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $valid->errors(),
            ], 422);
        }

        $data = $valid->validated();

        return DB::transaction(function () use ($request, $data) {
            $s = $this->current();

            // Update field teks langsung bila dikirim
            foreach (
                [
                    'telepon',
                    'facebook',
                    'instagram',
                    'twitter',
                    'linkedin',
                    'title_hero',
                    'desc_hero',
                    'desc_footer',
                ] as $f
            ) {
                if (array_key_exists($f, $data)) {
                    $s->{$f} = $data[$f];
                }
            }

            // Hapus logo jika diminta
            if (($data['clear_logo'] ?? false) === true) {
                if (!empty($s->logo_path)) Cloudinary::destroy($s->logo_path);
                $s->logo = null;
                $s->logo_path = null;
            }

            // Hapus favicon jika diminta
            if (($data['clear_favicon'] ?? false) === true) {
                if (!empty($s->favicon_path)) Cloudinary::destroy($s->favicon_path);
                $s->favicon = null;
                $s->favicon_path = null;
            }

            // Upload logo baru (jika ada)
            if ($request->hasFile('logo')) {
                if (!empty($s->logo_path)) Cloudinary::destroy($s->logo_path);
                [$url, $pid] = $this->uploadToCloudinary($request->file('logo'), 'enotaris/settings', 'logo_');
                $s->logo = $url;
                $s->logo_path = $pid;
            }

            // Upload favicon baru (jika ada)
            if ($request->hasFile('favicon')) {
                if (!empty($s->favicon_path)) Cloudinary::destroy($s->favicon_path);
                [$url, $pid] = $this->uploadToCloudinary($request->file('favicon'), 'enotaris/settings', 'favicon_');
                $s->favicon = $url;
                $s->favicon_path = $pid;
            }

            $s->save();

            return response()->json([
                'success' => true,
                'message' => 'Setting berhasil disimpan',
                'data'    => $s,
            ], 200);
        });
    }

    /** Helper upload */
    private function uploadToCloudinary($uploadedFile, string $folder, string $prefix): array
    {
        $filename = $prefix . now()->format('YmdHis');
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
