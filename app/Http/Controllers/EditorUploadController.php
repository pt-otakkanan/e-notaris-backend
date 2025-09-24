<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EditorUploadController extends Controller
{
    public function deleteImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'public_id' => 'required|string',
        ], [
            'public_id.required' => 'Public ID wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $publicId = $request->input('public_id');

            // Hapus di Cloudinary
            $result = Cloudinary::destroy($publicId);

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus.',
                'data'    => $result, // biasanya berisi ["result" => "ok"] kalau sukses
            ]);
        } catch (\Throwable $e) {
            Log::error('Editor image delete failed', [
                'msg'  => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload image dari Quill → Cloudinary → kembalikan URL.
     * Form field: "image"
     */
    public function uploadImage(Request $request)
    {
        // pastikan user login (auth:sanctum di route)
        $user = $request->user();

        // validasi file
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|mimes:jpg,jpeg,png,webp,gif|max:3072', // max 3 MB
        ], [
            'image.required' => 'File gambar wajib diunggah.',
            'image.mimes'    => 'Format harus JPG, JPEG, PNG, WEBP, atau GIF.',
            'image.max'      => 'Ukuran gambar maksimal 3MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('image');
            $folderDate = now()->format('Ymd');
            $folder = "enotaris/users/{$user->id}/editor/{$folderDate}";
            $publicId = Str::uuid()->toString();

            // Upload ke Cloudinary
            $uploaded = Cloudinary::upload(
                $file->getRealPath(),
                [
                    'folder'         => $folder . '/',
                    'public_id'      => $publicId,
                    'overwrite'      => true,
                    'resource_type'  => 'image',
                    'quality'        => 'auto:good',
                    'fetch_format'   => 'auto',
                    'timeout'        => 60,
                ]
            );

            $secureUrl = $uploaded->getSecurePath();

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil diunggah.',
                'data'    => [
                    'url'        => $secureUrl,
                    'public_id'  => $uploaded->getPublicId(),
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Editor image upload failed', [
                'msg'  => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
