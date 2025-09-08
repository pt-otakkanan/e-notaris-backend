<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;

class DocumentRequirementController extends Controller
{
    /**
     * GET /document-requirements
     * Query: activity_id, user_id, status, q, created_from, created_to, per_page
     * Akses: notaris pemilik activity ATAU user yang menjadi klien activity tsb.
     */
    public function index(Request $request)
    {
        $user      = $request->user();
        $perPage   = max(1, (int) $request->query('per_page', 10));
        $activityId = $request->query('activity_id');
        $userId    = $request->query('user_id');
        $status    = $request->query('status'); // pending|approved|rejected
        $q         = $request->query('q');
        $from      = $request->query('created_from'); // Y-m-d
        $to        = $request->query('created_to');   // Y-m-d

        $query = DocumentRequirement::with(['activity', 'user', 'requirement'])
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id)
                    ->orWhereHas('clients', function ($c) use ($user) {
                        $c->where('users.id', $user->id);
                    });
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

        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);

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

    /**
     * GET /document-requirements/by-activity/{id}
     * Akses: notaris pemilik atau klien pada activity tsb
     */
    public function getByActivity(Request $request, $id)
    {
        $user     = $request->user();
        $perPage  = max(1, (int) $request->query('per_page', 10));

        $userId = $request->query('user_id');
        $status = $request->query('status');
        $q      = $request->query('q');
        $from   = $request->query('created_from');
        $to     = $request->query('created_to');

        $query = DocumentRequirement::with(['activity', 'user', 'requirement'])
            ->where('activity_notaris_id', $id)
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id)
                    ->orWhereHas('clients', function ($c) use ($user) {
                        $c->where('users.id', $user->id);
                    });
            });

        if ($userId)   $query->where('user_id', $userId);
        if ($status)   $query->where('status_approval', $status);
        if ($q)        $query->where('value', 'like', "%{$q}%");
        if ($from)     $query->whereDate('created_at', '>=', $from);
        if ($to)       $query->whereDate('created_at', '<=', $to);

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

    /**
     * GET /document-requirements/by-activity/{id}/me
     * Klien: ambil semua requirement milik dirinya pada activity tsb.
     * Notaris: bisa pakai query user_id untuk melihat milik klien tertentu.
     */
    public function getRequirementByActivityUser(Request $request, $id)
    {
        $user = $request->user();

        // izinkan jika user adalah notaris pemilik atau klien activity
        $allowed = Activity::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_notaris_id', $user->id)
                    ->orWhereHas('clients', function ($c) use ($user) {
                        $c->where('users.id', $user->id);
                    });
            })->exists();

        if (!$allowed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke aktivitas ini',
                'data'    => null
            ], 403);
        }

        // Jika notaris → boleh override user_id via query; jika klien → pakai id sendiri
        $targetUserId = $request->query('user_id');
        if (!$targetUserId) {
            $targetUserId = $user->id;
        }

        $docs = DocumentRequirement::with(['activity', 'user', 'requirement'])
            ->where('activity_notaris_id', $id)
            ->where('user_id', $targetUserId)
            ->get();

        if ($docs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail dokumen persyaratan berhasil diambil',
            'data'    => $docs,
        ], 200);
    }

    /**
     * GET /document-requirements/{id}
     * Akses: notaris pemilik atau klien pada activity tsb
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $doc = DocumentRequirement::with(['activity', 'user', 'requirement'])
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id)
                    ->orWhereHas('clients', function ($c) use ($user) {
                        $c->where('users.id', $user->id);
                    });
            })
            ->find($id);

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
     * POST /document-requirements (multipart/form-data jika ada file)
     * Body:
     * - activity_notaris_id (required, exists:activity,id)
     * - user_id (opsional; klien diabaikan & dipaksa = user login)
     * - value (nullable|string)
     * - file  (nullable|file)
     */
    public function store(Request $request)
    {
        $user   = $request->user();

        $validator = Validator::make($request->all(), [
            'activity_notaris_id' => 'required|integer|exists:activity,id',
            'user_id'             => 'sometimes|integer|exists:users,id',
            'value'               => 'nullable|string|max:1000',
            'file'                => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        // Cek akses ke activity
        $activity = Activity::with('clients')->find($request->input('activity_notaris_id'));
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $isOwner = (int) $activity->user_notaris_id === (int) $user->id;
        $isClient = $activity->clients->contains('id', $user->id);

        if (!$isOwner && !$isClient) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke aktivitas ini.',
                'data'    => null,
            ], 403);
        }

        // Tentukan user_id target:
        // - Jika klien: paksa ke user login
        // - Jika notaris: boleh pakai user_id dari request (wajib anggota clients)
        $targetUserId = $user->id;
        if ($isOwner && $request->filled('user_id')) {
            $targetUserId = (int) $request->input('user_id');
            if (!$activity->clients->contains('id', $targetUserId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User target bukan klien pada aktivitas ini.',
                    'data'    => null,
                ], 422);
            }
        }

        if (!$request->filled('value') && !$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal isi salah satu: value atau file.',
                'data'    => null
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id']         = $targetUserId;
        $data['status_approval'] = 'pending';

        if ($request->hasFile('file')) {
            $publicId  = 'req_' . time() . '_' . Str::random(8);
            $folder    = "enotaris/activities/{$data['activity_notaris_id']}/requirements/{$targetUserId}";
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

        $doc = DocumentRequirement::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen persyaratan berhasil dibuat',
            'data'    => $doc,
        ], 201);
    }

    /**
     * PUT/PATCH /document-requirements/{id}
     * Catatan: reset status_approval ke 'pending' setiap ada perubahan konten.
     */
    public function update(Request $request, $id)
    {
        $doc = DocumentRequirement::with(['activity.clients'])->find($id);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $user = $request->user();
        $activity = $doc->activity;

        // Akses: notaris pemilik atau pemilik dokumen (user_id sama)
        $isOwner  = (int) $activity->user_notaris_id === (int) $user->id;
        $isAuthor = (int) $doc->user_id === (int) $user->id;

        if (!$isOwner && !$isAuthor) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah dokumen ini.',
                'data'    => null
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'activity_notaris_id' => 'sometimes|integer|exists:activity,id',
            'user_id'             => 'sometimes|integer|exists:users,id',
            'value'               => 'nullable|string|max:1000',
            'file'                => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Notaris boleh ganti user_id hanya ke klien activity
        if (array_key_exists('user_id', $data) && !$isOwner) {
            unset($data['user_id']); // klien tidak boleh pindahkan kepemilikan
        }
        if ($isOwner && array_key_exists('user_id', $data)) {
            if (!$activity->clients->contains('id', (int) $data['user_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'User target bukan klien pada aktivitas ini.',
                    'data'    => null,
                ], 422);
            }
        }

        // File baru → hapus file lama
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
            $ownerId    = $data['user_id'] ?? $doc->user_id;

            $publicId  = 'req_' . time() . '_' . Str::random(8);
            $folder    = "enotaris/activities/{$activityId}/requirements/{$ownerId}";
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

        // Reset status setiap ada perubahan
        $doc->status_approval = 'pending';

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
     * Akses: notaris pemilik atau pemilik dokumen
     */
    public function destroy($id)
    {
        $doc = DocumentRequirement::with('activity')->find($id);
        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $user = request()->user();
        $isOwner  = (int) $doc->activity->user_notaris_id === (int) $user->id;
        $isAuthor = (int) $doc->user_id === (int) $user->id;

        if (!$isOwner && !$isAuthor) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses menghapus dokumen ini.',
                'data'    => null
            ], 403);
        }

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

    /**
     * PUT /document-requirements/{id}/approval
     * Akses: notaris pemilik activity.
     */
    public function approval(Request $request, $id)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status_approval' => 'required|in:approved,rejected',
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
            })
            ->find($id);

        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $doc->status_approval = $request->input('status_approval');
        $doc->save();

        return response()->json([
            'success' => true,
            'message' => 'Status approval dokumen persyaratan berhasil diperbarui',
            'data'    => $doc
        ], 200);
    }

    public function getRequirementByActivityNotarisForUser(Request $request, $idActivity, $idUser)
    {
        $user = $request->user();

        $activity = Activity::with(['clients:id'])
            ->where('id', $idActivity)
            ->where('user_notaris_id', $user->id) // must be owner
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity tidak ditemukan atau Anda bukan pemilik.',
                'data'    => null,
            ], 404);
        }

        // pastikan target user adalah klien di activity
        $isClient = $activity->clients->contains('id', (int) $idUser);
        if (!$isClient) {
            return response()->json([
                'success' => false,
                'message' => 'User target bukan klien pada aktivitas ini.',
                'data'    => null,
            ], 422);
        }

        $docs = DocumentRequirement::with(['requirement'])
            ->where('activity_notaris_id', $activity->id)
            ->where('user_id', (int) $idUser)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar requirement untuk klien berhasil diambil',
            'data'    => $docs,
        ], 200);
    }

    public function getRequirementByActivityNotaris(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::with(['clients:id,name,email'])
            ->where('id', $id)
            ->where('user_notaris_id', $user->id) // pastikan owner
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity tidak ditemukan atau Anda bukan pemilik.',
                'data'    => null,
            ], 404);
        }

        // build options klien untuk FE
        $users = $activity->clients->map(function ($u) {
            return [
                'value' => $u->id,
                'label' => $u->name ? "{$u->name} ({$u->email})" : ($u->email ?? "#{$u->id}"),
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
            ];
        })->values();

        // (opsional) ringkasan jumlah dokumen per user
        // $summary = DocumentRequirement::select('user_id')
        //     ->where('activity_notaris_id', $activity->id)
        //     ->selectRaw('COUNT(*) as total')
        //     ->selectRaw("SUM(CASE WHEN status_approval='approved' THEN 1 ELSE 0 END) as approved")
        //     ->selectRaw("SUM(CASE WHEN status_approval='rejected' THEN 1 ELSE 0 END) as rejected")
        //     ->groupBy('user_id')
        //     ->get();

        return response()->json([
            'success'  => true,
            'message'  => 'Data requirement per aktivitas (notaris) berhasil diambil',
            'data'     => [
                'activity' => [
                    'id'   => $activity->id,
                    'name' => $activity->name,
                    'deed' => $activity->deed ?? null, // kalau mau load deed, tambahkan with('deed')
                ],
                'users'    => $users,
                // 'summary'  => $summary,
            ],
        ], 200);
    }
}
