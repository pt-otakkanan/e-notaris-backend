<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;

class DocumentRequirementController extends Controller
{
    /**
     * GET /document-requirements
     * Query opsional:
     * - activity_id (int)
     * - user_id (int)
     * - status (pending|approved|rejected)
     * - q (search di value)
     * - created_from (Y-m-d)
     * - created_to   (Y-m-d)
     * - per_page (default 10)
     *
     * Catatan: default membatasi data hanya untuk activity milik notaris-login
     * (activity.user_notaris_id = $user->id). Hilangkan blok whereHas jika tidak diperlukan.
     */
    public function index(Request $request)
    {
        $user      = $request->user();
        $perPage   = (int)($request->query('per_page', 10));
        $perPage   = $perPage > 0 ? $perPage : 10;
        $activityId = $request->query('activity_id');
        $userId    = $request->query('user_id');
        $status    = $request->query('status'); // pending|approved|rejected
        $q         = $request->query('q');
        $from      = $request->query('created_from'); // Y-m-d
        $to        = $request->query('created_to');   // Y-m-d

        $query = DocumentRequirement::with(['activity', 'user'])
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id)->orWhere('first_client_id', $user->id)->orWhere('second_client_id', $user->id);
            });

        if ($activityId) {
            $query->where('activity_notaris_id', $activityId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status_approval', $status);
        }

        if ($q) {
            $query->where('value', 'like', "%{$q}%");
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar dokumen persyaratan berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ]
        ], 200);
    }

    public function getByActivity(Request $request, $id)
    {
        $user     = $request->user();
        $perPage  = (int)($request->query('per_page', 10));
        $perPage  = $perPage > 0 ? $perPage : 10;

        // optional filters
        $userId = $request->query('user_id');      // filter per penghadap tertentu
        $status = $request->query('status');       // pending|approved|rejected
        $q      = $request->query('q');            // search di value
        $from   = $request->query('created_from'); // Y-m-d
        $to     = $request->query('created_to');   // Y-m-d

        $query = DocumentRequirement::with(['activity', 'user', 'requirement'])
            // kunci ke activity dari path param
            ->where('activity_notaris_id', $id)
            // authorisasi: notaris pemilik atau salah satu client di activity tsb
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where(function ($s) use ($user) {
                    $s->where('user_notaris_id', $user->id);
                });
            });

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status_approval', $status);
        }

        if ($q) {
            $query->where('value', 'like', "%{$q}%");
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar dokumen persyaratan per aktivitas berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ]
        ], 200);
    }

    public function getRequirementByActivityUser(Request $request, $id)
    {
        $user = $request->user();

        // Opsi 1: Query berdasarkan activity_id dan user authorization
        $doc = DocumentRequirement::whereHas('activity', function ($sub) use ($user, $id) {
            $sub->where('id', $id)
                ->where(function ($query) use ($user) {
                    $query->where('first_client_id', $user->id)
                        ->orWhere('second_client_id', $user->id);
                });
        })->get();

        // Atau Opsi 2: Query langsung dengan activity_id
        // $doc = DocumentRequirement::with(['activity'])
        //     ->where('activity_id', $id)
        //     ->whereHas('activity', function ($sub) use ($user) {
        //         $sub->where(function($query) use ($user) {
        //             $query->where('first_client_id', $user->id)
        //                   ->orWhere('second_client_id', $user->id);
        //         });
        //     })->get();

        if ($doc->isEmpty()) { // Gunakan isEmpty() untuk Collection
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan atau Anda tidak memiliki akses',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail dokumen persyaratan berhasil diambil',
            'data'    => $doc
        ], 200);
    }

    /**
     * GET /document-requirements/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $doc = DocumentRequirement::with(['activity', 'user'])
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id)->orWhere('first_client_id', $user->id)->orWhere('second_client_id', $user->id);
            })->find($id);

        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail dokumen persyaratan berhasil diambil',
            'data'    => $doc
        ], 200);
    }

    /**
     * POST /document-requirements
     * Body:
     * - activity_notaris_id (required)
     * - user_id (opsional, default: user login)
     * - value (nullable|string)
     * - file  (nullable|file)  // multipart/form-data
     * - status_approval (opsional; default 'pending')
     *
     * Aturan: minimal salah satu dari 'value' atau 'file' harus diisi.
     */
    public function store(Request $request)
    {
        $user   = $request->user();
        $userId = (int)($request->input('user_id', $user->id));

        $validator = Validator::make($request->all(), [
            'activity_notaris_id' => 'required|integer|exists:activity,id',
            'user_id'             => 'sometimes|integer|exists:users,id',
            'value'               => 'nullable|string|max:1000',
            'file'                => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:2120',
        ], [
            'activity_notaris_id.required' => 'Activity wajib diisi.',
            'activity_notaris_id.exists'   => 'Activity tidak ditemukan.',
            'value.string'                 => 'Value harus berupa teks.',
            'value.max'                    => 'Value maksimal 1000 karakter.',
            'file.file'                    => 'File tidak valid.',
            'file.mimes'                   => 'Tipe file tidak didukung.',
            'file.max'                     => 'Ukuran file maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        // Pastikan minimal satu: value atau file
        if (!$request->filled('value') && !$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal isi salah satu: value atau file.',
                'data'    => ['value' => ['Wajib diisi jika tidak mengirim file'], 'file' => ['Wajib diisi jika tidak mengirim value']]
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $userId;
        $data['status_approval'] = 'pending';

        // Upload file (jika ada)
        if ($request->hasFile('file')) {
            $publicId  = 'req_' . time() . '_' . Str::random(8);
            $folder    = "enotaris/activities/{$data['activity_notaris_id']}/requirements/{$userId}";
            $uploaded  = Cloudinary::upload(
                $request->file('file')->getRealPath(),
                [
                    'folder'        => $folder,
                    'public_id'     => $publicId,
                    'overwrite'     => true,
                    'resource_type' => 'auto',
                    'quality'       => 'auto:good',
                ]
            );

            $data['file']      = $uploaded->getSecurePath(); // URL https
            $data['file_path'] = $uploaded->getPublicId();   // simpan public_id untuk destroy
        }

        $doc = DocumentRequirement::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil dibuat',
            'data'    => $doc,
        ], 201);
    }

    /**
     * PUT/PATCH /document-requirements/{id}
     * Partial update:
     * - activity_notaris_id (opsional)
     * - user_id (opsional)
     * - value (nullable|string)
     * - file  (nullable|file) // jika kirim file baru, file lama dihapus dari Cloudinary
     * - status_approval (pending|approved|rejected)
     */
    public function update(Request $request, $id)
    {
        $doc = DocumentRequirement::find($id);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'activity_notaris_id' => 'sometimes|integer|exists:activity,id',
            'user_id'             => 'sometimes|integer|exists:users,id',
            'value'               => 'nullable|string|max:1000',
            'file'                => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
        ], [
            'activity_notaris_id.integer' => 'Activity harus berupa angka.',
            'activity_notaris_id.exists'  => 'Activity tidak ditemukan.',
            'user_id.integer'             => 'User harus berupa angka.',
            'user_id.exists'              => 'User tidak ditemukan.',
            'value.string'                => 'Value harus berupa teks.',
            'value.max'                   => 'Value maksimal 1000 karakter.',
            'file.file'                   => 'File tidak valid.',
            'file.mimes'                  => 'Tipe file tidak didukung.',
            'file.max'                    => 'Ukuran file maksimal 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Jika user mengganti file, hapus file lama
        if ($request->hasFile('file')) {
            if (!empty($doc->file_path)) {
                try {
                    Cloudinary::destroy($doc->file_path);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menghapus file lama.',
                    ], 500);
                }
            }

            $activityId = $data['activity_notaris_id'] ?? $doc->activity_notaris_id;
            $userId     = $data['user_id'] ?? $doc->user_id;

            $publicId  = 'req_' . time() . '_' . Str::random(8);
            $folder    = "enotaris/activities/{$activityId}/requirements/{$userId}";
            $uploaded  = Cloudinary::upload(
                $request->file('file')->getRealPath(),
                [
                    'folder'        => $folder,
                    'public_id'     => $publicId,
                    'overwrite'     => true,
                    'resource_type' => 'auto',
                    'quality'       => 'auto:good',
                ]
            );

            $data['file']      = $uploaded->getSecurePath();
            $data['file_path'] = $uploaded->getPublicId();
        }

        foreach (['activity_notaris_id', 'user_id', 'value', 'file', 'file_path'] as $f) {
            if (array_key_exists($f, $data)) {
                $doc->{$f} = $data[$f];
            }
        }

        // Reset status approval ke pending setiap update
        $doc->status_approval = 'pending';

        // Jika setelah update, value & file sama-sama kosong → tolak
        if (is_null($doc->value) && empty($doc->file)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal value atau file harus ada.',
                'data'    => null
            ], 422);
        }

        $doc->save();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil diperbarui',
            'data'    => $doc
        ], 200);
    }


    /**
     * DELETE /document-requirements/{id}
     */
    public function destroy($id)
    {
        $doc = DocumentRequirement::find($id);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // Hapus file di Cloudinary jika ada
        if (!empty($doc->file_path)) {
            try {
                Cloudinary::destroy($doc->file_path);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus file dari penyimpanan.',
                    'data'    => null
                ], 500);
            }
        }

        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil dihapus',
            'data'    => null
        ], 200);
    }

    public function approval(Request $request, $id)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status_approval' => 'required|in:approved,rejected',
        ], [
            'status_approval.required' => 'Status approval wajib diisi.',
            'status_approval.in' => 'Status approval harus approved atau rejected.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        $doc = DocumentRequirement::with(['activity', 'user'])
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id);
            })->find($id);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $status = $request->input('status_approval');
        $doc->status_approval = $status;
        $doc->save();
        return response()->json([
            'success' => true,
            'message' => 'Status approval dokumen persyaratan berhasil diperbarui',
            'data'    => $doc
        ], 200);
    }
}
