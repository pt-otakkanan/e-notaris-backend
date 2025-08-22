<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\Identity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

        $query = User::where('role_id', '!=', 1);

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
        $user = User::where('id', $id)->first();

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
            ]
        ], 200);
    }

    public function destroyUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan',
            ], 404);
        }

        // Hapus avatar jika ada
        if (!empty($user->file_avatar_path)) {
            Cloudinary::destroy($user->file_avatar_path);
        }


        // Hapus data identitas jika ada
        $identity = Identity::where('user_id', $user->id);

        if ($identity) {
            foreach (['file_ktp_path', 'file_kk_path', 'file_npwp_path', 'file_ktp_notaris_path', 'file_sign_path'] as $field) {
                if (!empty($identity->{$field})) {
                    Cloudinary::destroy($identity->{$field});
                }
            }
        }
        $identity->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus',
        ], 200);
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
                    'ktp' => $identity->ktp,
                    'npwp' => $identity->npwp,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp' => $identity->file_ktp,
                    'file_kk' => $identity->file_kk,
                    'file_npwp' => $identity->file_npwp,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_sign' => $identity->file_sign,
                    'created_at' => $identity->created_at,
                    'updated_at' => $identity->updated_at,
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
        // Tambahkan validasi yang lebih ketat
        $validator = Validator::make($request->all(), [
            'ktp' => 'required|string|max:16', // WAJIB
            'npwp' => 'sometimes|string|max:15',
            'ktp_notaris' => 'sometimes|string|max:16',
            'file_ktp' => 'required|file|image|mimes:jpg,jpeg,png,pdf|max:2048', // WAJIB
            'file_kk' => 'required|file|image|mimes:jpg,jpeg,png,pdf|max:2048', // WAJIB
            'file_npwp' => 'sometimes|file|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_ktp_notaris' => 'sometimes|file|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_sign' => 'required|file|image|mimes:png|max:1024', // WAJIB dan hanya PNG
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Cek apakah identity sudah ada
            $identity = Identity::where('user_id', $user->id)->first();

            if (!$identity) {
                $identity = new Identity();
                $identity->user_id = $user->id;
            }

            // Update field biasa
            $identity->ktp = $request->ktp ?? $identity->ktp;
            $identity->npwp = $request->npwp ?? $identity->npwp;
            $identity->ktp_notaris = $request->ktp_notaris ?? $identity->ktp_notaris;

            // === Upload files ke Cloudinary dengan timeout dan error handling ===
            $fileFields = [
                'file_ktp' => 'ktp',
                'file_kk' => 'kk',
                'file_npwp' => 'npwp',
                'file_ktp_notaris' => 'ktp_notaris',
                'file_sign' => 'sign'
            ];

            foreach ($fileFields as $fileField => $folder) {
                if ($request->hasFile($fileField)) {
                    try {
                        // Hapus file lama jika ada
                        $pathField = $fileField . '_path';
                        if (!empty($identity->{$pathField})) {
                            try {
                                Cloudinary::destroy($identity->{$pathField});
                            } catch (\Exception $e) {

                                return response()->json([
                                    'success' => false,
                                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                                ], 500);
                            }
                        }

                        // Upload file baru dengan konfigurasi timeout
                        $uploaded = Cloudinary::upload(
                            $request->file($fileField)->getRealPath(),
                            [
                                'folder' => "enotaris/users/{$user->id}/identity/{$folder}",
                                'public_id' => $folder . '_' . time() . '_' . Str::random(8),
                                'overwrite' => true,
                                'resource_type' => 'auto', // auto detect image/raw
                                'timeout' => 30, // timeout 30 detik per file
                                'quality' => 'auto:good', // compress otomatis
                            ]
                        );

                        // Simpan URL dan path
                        $identity->{$fileField} = $uploaded->getSecurePath();
                        $identity->{$pathField} = $uploaded->getPublicId();
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => "Gagal mengupload {$fileField}: " . $e->getMessage()
                        ], 500);
                    }
                }
            }

            $identity->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil identitas berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telepon' => $user->telepon,
                    'gender' => $user->gender,
                    'address' => $user->address,
                    'file_avatar' => $user->file_avatar,
                    'file_avatar_path' => $user->file_avatar_path,

                    'ktp' => $identity->ktp,
                    'file_ktp' => $identity->file_ktp,
                    'file_ktp_path' => $identity->file_ktp_path,
                    'file_kk' => $identity->file_kk,
                    'file_kk_path' => $identity->file_kk_path,
                    'npwp' => $identity->npwp,
                    'file_npwp' => $identity->file_npwp,
                    'file_npwp_path' => $identity->file_npwp_path,
                    'ktp_notaris' => $identity->ktp_notaris,
                    'file_ktp_notaris' => $identity->file_ktp_notaris,
                    'file_ktp_notaris_path' => $identity->file_ktp_notaris_path,
                    'file_sign' => $identity->file_sign,
                    'file_sign_path' => $identity->file_sign_path,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
}
