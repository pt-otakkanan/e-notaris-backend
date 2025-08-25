<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\Activity;
use App\Models\Identity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $perPage = (int)($request->query('per_page', 10));
        $perPage = $perPage > 0 ? $perPage : 10;
        $q = $request->query('q');

        $query = User::with(['roles', 'identity'])->where('role_id', '!=', 1);

        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telepon', 'like', "%{$q}%"); // optional
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Data semua pengguna berhasil diambil',
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ]
        ], 200);
    }

    public function getDetailUser(Request $request, $id)
    {
        $user = User::with(['roles', 'identity'])->where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan',
                'data' => null
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telepon' => $user->telepon,
                'gender' => $user->gender,
                'address' => $user->address,
                'file_avatar' => $user->file_avatar,
                'status_verification' => $user->status_verification,
                'notes_verification' => $user->notes_verification,
                'created_at' => $user->created_at,
                'identity' => [
                    'ktp'              => $user->identity?->ktp,
                    'npwp'             => $user->identity?->npwp,
                    'ktp_notaris'      => $user->identity?->ktp_notaris,
                    'file_ktp'         => $user->identity?->file_ktp,
                    'file_kk'          => $user->identity?->file_kk,
                    'file_npwp'        => $user->identity?->file_npwp,
                    'file_ktp_notaris' => $user->identity?->file_ktp_notaris,
                    'file_sign'        => $user->identity?->file_sign,
                    'file_photo'       => $user->identity?->file_photo,
                    'created_at'       => $user->identity?->created_at,
                    'updated_at'       => $user->identity?->updated_at,
                ]
            ]
        ], 200);
    }

    public function destroyUser(Request $request, $id)
    {
        // Pastikan routes endpoint ini dibatasi ability:admin
        $user = User::with(['identity', 'documentRequirements'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // 1) Hapus semua ACTIVITY yang mereferensikan user ini
            Activity::where(function ($q) use ($user) {
                $q->where('first_client_id',  $user->id)
                    ->orWhere('second_client_id', $user->id)
                    ->orWhere('user_notaris_id',  $user->id);
            })->delete();
            // NOTE: Jika tabel lain mereferensikan activity (child), pastikan FK-nya ON DELETE CASCADE,
            // atau hapus child-nya dulu di sini.

            // 2) Hapus DOCUMENT REQUIREMENTS milik user (kalau ada file ter-Cloudinary, bersihkan juga di sini)
            //    Contoh ini hanya delete row; tambahkan Cloudinary::destroy() kalau ada path public_id.
            $user->documentRequirements()->delete();

            // 3) Hapus IDENTITY + file Cloudinary terkait
            if ($user->identity) {
                foreach (
                    [
                        'file_ktp_path',
                        'file_kk_path',
                        'file_npwp_path',
                        'file_ktp_notaris_path',
                        'file_sign_path',
                        'file_photo_path', // ⬅️ jangan lupa foto formal
                    ] as $field
                ) {
                    $publicId = $user->identity->{$field} ?? null;
                    if (!empty($publicId)) {
                        try {
                            Cloudinary::destroy($publicId);
                        } catch (\Throwable $e) {
                            // optional: log
                        }
                    }
                }
                $user->identity()->delete();
            }

            // 4) Hapus avatar user di Cloudinary kalau ada
            if (!empty($user->file_avatar_path)) {
                try {
                    Cloudinary::destroy($user->file_avatar_path);
                } catch (\Throwable $e) {
                    // optional: log
                }
            }

            // 5) (Optional) Revoke semua token Sanctum user
            try {
                $user->tokens()->delete();
            } catch (\Throwable $e) {
            }

            // 6) Hapus user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengguna beserta relasinya berhasil dihapus.',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengguna. ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getProfile(Request $request)
    {
        $user = $request->user();
        $identity = Identity::where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => [
                // Data User
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telepon' => $user->telepon,
                    'gender' => $user->gender,
                    'address' => $user->address,
                    'file_avatar' => $user->file_avatar,
                    'status_verification' => $user->status_verification,
                    'notes_verification' => $user->notes_verification,
                    'created_at' => $user->created_at,
                ],

                // Data Identity
                'identity' => [
                    'ktp'              => $identity?->ktp,
                    'npwp'             => $identity?->npwp,
                    'ktp_notaris'      => $identity?->ktp_notaris,
                    'file_ktp'         => $identity?->file_ktp,
                    'file_kk'          => $identity?->file_kk,
                    'file_npwp'        => $identity?->file_npwp,
                    'file_ktp_notaris' => $identity?->file_ktp_notaris,
                    'file_sign'        => $identity?->file_sign,
                    'file_photo'       => $identity?->file_photo,
                    'created_at'       => $identity?->created_at,
                    'updated_at'       => $identity?->updated_at,
                ]
            ]
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        // validasi konsisten
        $validasi = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'gender'      => 'sometimes|string|in:male,female,lainnya', // sesuaikan opsi
            'telepon'     => 'sometimes|string|max:50',
            'address'     => 'sometimes|string|max:255',
            'file_avatar' => 'sometimes|file|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $user = $request->user();

        // update field teks
        foreach (['name', 'gender', 'telepon', 'address'] as $f) {
            if ($request->filled($f)) {
                $user->{$f} = $request->input($f);
            }
        }

        // upload avatar ke Cloudinary
        if ($request->hasFile('file_avatar')) {
            // hapus lama kalau ada
            if (!empty($user->file_avatar_path)) {
                // di contohmu: destroy pakai path/public_id
                Cloudinary::destroy($user->file_avatar_path);
            }

            // siapkan folder & public_id
            $imageName    = $user->id . '_' . now()->format('YmdHis');
            $folder       = "enotaris/users/{$user->id}/profile";
            $publicIdFull = $folder . '/' . $imageName;

            // upload
            $uploaded = Cloudinary::upload(
                $request->file('file_avatar')->getRealPath(),
                [
                    'folder'     => $folder . '/',
                    'public_id'  => $imageName,
                    'overwrite'  => true,
                    'resource_type' => 'image',
                ]
            );

            // simpan URL + path (public_id)
            $user->file_avatar      = $uploaded->getSecurePath(); // URL https
            $user->file_avatar_path = $publicIdFull;              // untuk destroy
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'telepon'       => $user->telepon,
                'gender'        => $user->gender,
                'address'       => $user->address,
                'file_avatar'   => $user->file_avatar,
                'file_avatar_path' => $user->file_avatar_path,
            ]
        ], 200);
    }

    public function updateIdentityProfile(Request $request)
    {
        // Validasi (PDF diperbolehkan untuk KTP/KK/NPWP/KTP Notaris → JANGAN pakai 'image')
        $validator = Validator::make($request->all(), [
            'ktp'            => 'required|string|max:16',
            'npwp'           => 'sometimes|nullable|string|max:20',
            'ktp_notaris'    => 'sometimes|nullable|string|max:16',

            // kalau izinkan pdf, hapus 'image', cukup 'mimes' atau 'mimetypes'
            'file_ktp'         => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_kk'          => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_npwp'        => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp_notaris' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:2048',
            // tanda tangan wajib PNG
            'file_sign'        => 'sometimes|file|mimes:png|max:1024',
            // foto formal hanya image (tanpa pdf)
            'file_photo'       => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            return DB::transaction(function () use ($request, $user) {

                // ambil / buat identity
                $identity = \App\Models\Identity::where('user_id', $user->id)->first();
                if (!$identity) {
                    $identity = new \App\Models\Identity();
                    $identity->user_id = $user->id;
                }

                $hasChanges = false;

                // --- update field sederhana
                if ($request->filled('ktp') && $request->ktp !== $identity->ktp) {
                    $identity->ktp = $request->ktp;
                    $hasChanges = true;
                }
                if ($request->has('npwp') && $request->npwp !== $identity->npwp) {
                    $identity->npwp = $request->npwp;
                    $hasChanges = true;
                }
                if ($request->has('ktp_notaris') && $request->ktp_notaris !== $identity->ktp_notaris) {
                    $identity->ktp_notaris = $request->ktp_notaris;
                    $hasChanges = true;
                }

                // --- upload helper
                $fileFields = [
                    'file_ktp'         => 'ktp',
                    'file_kk'          => 'kk',
                    'file_npwp'        => 'npwp',
                    'file_ktp_notaris' => 'ktp_notaris',
                    'file_sign'        => 'sign',
                    'file_photo'       => 'photo',
                ];

                foreach ($fileFields as $fileField => $folder) {
                    if ($request->hasFile($fileField)) {
                        // hapus lama kalau ada
                        $pathField = $fileField . '_path';
                        if (!empty($identity->{$pathField})) {
                            try {
                                Cloudinary::destroy($identity->{$pathField});
                            } catch (\Exception $e) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Terjadi kesalahan saat menghapus file lama.',
                                ], 500);
                            }
                        }

                        // upload baru
                        $uploaded = Cloudinary::upload(
                            $request->file($fileField)->getRealPath(),
                            [
                                'folder'       => "enotaris/users/{$user->id}/identity/{$folder}",
                                'public_id'    => $folder . '_' . time() . '_' . Str::random(8),
                                'overwrite'    => true,
                                'resource_type' => 'auto',
                                'timeout'      => 30,
                                'quality'      => 'auto:good',
                            ]
                        );

                        $identity->{$fileField} = $uploaded->getSecurePath();
                        $identity->{$pathField} = $uploaded->getPublicId();
                        $hasChanges = true;
                    }
                }

                // simpan identity
                $identity->save();

                // --- SET STATUS VERIFIKASI KE PENDING JIKA ADA PERUBAHAN
                if ($hasChanges && $user->status_verification !== 'pending') {
                    $user->status_verification = 'pending';
                    $user->notes_verification  = null; // kosongkan catatan lama
                    $user->save(); // PENTING: simpan user!
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Profil identitas berhasil diperbarui',
                    'data' => [
                        'id'                 => $user->id,
                        'name'               => $user->name,
                        'email'              => $user->email,
                        'telepon'            => $user->telepon,
                        'gender'             => $user->gender,
                        'address'            => $user->address,
                        'file_avatar'        => $user->file_avatar,
                        'file_avatar_path'   => $user->file_avatar_path,

                        'ktp'                => $identity->ktp,
                        'file_ktp'           => $identity->file_ktp,
                        'file_ktp_path'      => $identity->file_ktp_path,
                        'file_kk'            => $identity->file_kk,
                        'file_kk_path'       => $identity->file_kk_path,
                        'npwp'               => $identity->npwp,
                        'file_npwp'          => $identity->file_npwp,
                        'file_npwp_path'     => $identity->file_npwp_path,
                        'ktp_notaris'        => $identity->ktp_notaris,
                        'file_ktp_notaris'   => $identity->file_ktp_notaris,
                        'file_ktp_notaris_path' => $identity->file_ktp_notaris_path,
                        'file_sign'          => $identity->file_sign,
                        'file_sign_path'     => $identity->file_sign_path,
                        'file_photo'         => $identity->file_photo,
                        'file_photo_path'    => $identity->file_photo_path,

                        // tambahkan status user sekarang kalau perlu di FE
                        'status_verification' => $user->status_verification,
                        'notes_verification' => $user->notes_verification,
                    ],
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
}
