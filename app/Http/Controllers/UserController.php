<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{
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
}
