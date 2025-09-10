<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\DraftDeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// Sesuaikan namespace Cloudinary yg kamu pakai:
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
// App\Http\Controllers\DraftController.php
use Barryvdh\DomPDF\Facade\Pdf; // pastikan facade aktif
use Illuminate\Support\Facades\Storage;

class DraftController extends Controller
{
    /**
     * GET /draft
     * Query: search (by activity name / tracking_code), per_page
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $q       = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        // Join activity agar bisa filter kepemilikan notaris & pencarian
        $query = DraftDeed::with(['activity:id,name,tracking_code,user_notaris_id'])
            ->join('activities', 'draft_deeds.activity_id', '=', 'activities.id')
            ->select('draft_deeds.*');

        // Notaris hanya melihat draft di activity miliknya
        if ($user->role_id !== 1) { // 1 = admin
            $query->where('activities.user_notaris_id', $user->id);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('activities.name', 'like', "%{$q}%")
                    ->orWhere('activities.tracking_code', 'like', "%{$q}%");
            });
        }

        $drafts = $query->orderBy('draft_deeds.created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar draft berhasil diambil',
            'data'    => $drafts->items(),
            'meta'    => [
                'current_page' => $drafts->currentPage(),
                'per_page'     => $drafts->perPage(),
                'total'        => $drafts->total(),
                'last_page'    => $drafts->lastPage(),
            ],
        ], 200);
    }

    /**
     * GET /draft/{id}
     */
    public function show(Request $request, $id)
    {
        $user  = $request->user();
        $draft = DraftDeed::with([
            'activity' => function ($q) {
                $q->with(['deed', 'notaris', 'clients.identity', 'clientActivities']);
            }
        ])->find($id);

        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak berhak mengakses draft ini',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail draft berhasil diambil',
            'data'    => $draft,
        ], 200);
    }

    /**
     * POST /draft
     * Body:
     *  - activity_id (required, milik notaris ini)
     *  - custom_value_template (nullable, long html)
     *  - reading_schedule (nullable, date)
     *  - status_approval (optional: pending/approved/rejected)
     *  - file (optional upload: pdf/doc/docx/jpg/jpeg/png/webp)
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'activity_id'           => 'required|exists:activities,id',
            'custom_value_template' => 'nullable|string',
            'reading_schedule'      => 'nullable|date',
            'status_approval'       => 'sometimes|in:pending,approved,rejected',
            'file'                  => 'sometimes|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $user     = $request->user();
        $activity = Activity::find($request->input('activity_id'));
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }
        if ($user->role_id !== 1 && $activity->user_notaris_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tidak berhak menambahkan draft untuk aktivitas ini'], 403);
        }

        $data = $validasi->validated();

        // Upload file (opsional)
        $fileUrl  = null;
        $filePath = null;

        if ($request->hasFile('file')) {
            $folder   = "enotaris/activities/{$activity->id}/drafts";
            $filename = 'draft_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $request->file('file')->getRealPath(),
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'auto', // dukung pdf/img/doc
                ]
            );

            $fileUrl  = $upload->getSecurePath();
            $filePath = $publicId; // simpan public_id untuk destroy
        }

        $draft = DraftDeed::create([
            'activity_id'           => $activity->id,
            'custom_value_template' => $data['custom_value_template'] ?? null,
            'reading_schedule'      => $data['reading_schedule'] ?? null,
            'status_approval'       => $data['status_approval'] ?? 'pending',
            'file'                  => $fileUrl,
            'file_path'             => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil dibuat',
            'data'    => $draft->load('activity:id,name,tracking_code'),
        ], 201);
    }

    /**
     * POST /draft/update/{id}  (atau PUT /draft/{id})
     */
    public function update(Request $request, $id)
    {
        $draft = DraftDeed::with('activity')->find($id);
        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak berhak mengubah draft ini',
            ], 403);
        }

        $validasi = Validator::make($request->all(), [
            'custom_value_template' => 'sometimes|nullable|string',
            'reading_schedule'      => 'sometimes|nullable|date',
            'status_approval'       => 'sometimes|in:pending,approved,rejected',
            'file'                  => 'sometimes|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            // opsional: hapus file lama tanpa mengunggah baru
            'clear_file'            => 'sometimes|boolean',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        // Update kolom biasa
        foreach (['custom_value_template', 'reading_schedule', 'status_approval'] as $f) {
            if (array_key_exists($f, $data)) {
                $draft->{$f} = $data[$f];
            }
        }

        // Handle file
        if (($data['clear_file'] ?? false) === true) {
            if (!empty($draft->file_path)) {
                Cloudinary::destroy($draft->file_path);
            }
            $draft->file = null;
            $draft->file_path = null;
        }

        if ($request->hasFile('file')) {
            // hapus lama
            if (!empty($draft->file_path)) {
                Cloudinary::destroy($draft->file_path);
            }

            $folder   = "enotaris/activities/{$draft->activity_id}/drafts";
            $filename = 'draft_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $request->file('file')->getRealPath(),
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'auto',
                ]
            );

            $draft->file      = $upload->getSecurePath();
            $draft->file_path = $publicId;
        }

        $draft->save();

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil diperbarui',
            'data'    => $draft->load('activity:id,name,tracking_code'),
        ], 200);
    }

    /**
     * DELETE /draft/{id}
     */
    public function destroy(Request $request, $id)
    {
        $draft = DraftDeed::with('activity')->find($id);

        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak berhak menghapus draft ini',
            ], 403);
        }

        try {
            DB::transaction(function () use ($draft) {
                if (!empty($draft->file_path)) {
                    Cloudinary::destroy($draft->file_path);
                }
                $draft->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil dihapus',
                'data'    => null,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus draft: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function renderPdf(Request $request, $id)
    {
        $draft = DraftDeed::with('activity')->find($id);
        if (!$draft) {
            return response()->json(['success' => false, 'message' => 'Draft tidak ditemukan'], 404);
        }

        $user = $request->user();
        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
        }

        // Ambil HTML dari request atau pakai yang tersimpan
        $html = $request->input('html', $draft->custom_value_template ?? '');
        if (!trim($html)) {
            return response()->json(['success' => false, 'message' => 'HTML kosong'], 422);
        }

        // Render PDF
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        // Simpan sementara ke storage/tmp
        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
        $tmpFile = $tmpDir . '/draft_' . $draft->id . '_' . time() . '.pdf';
        file_put_contents($tmpFile, $pdf->output());

        // Upload ke Cloudinary
        try {
            // hapus file lama
            if (!empty($draft->file_path)) {
                Cloudinary::destroy($draft->file_path);
            }

            $folder   = "enotaris/activities/{$draft->activity_id}/drafts";
            $filename = 'draft_pdf_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $tmpFile,
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'auto',
                ]
            );

            // update kolom file
            $draft->file      = $upload->getSecurePath();
            $draft->file_path = $publicId;
            $draft->save();
        } finally {
            if (file_exists($tmpFile)) @unlink($tmpFile);
        }

        return response()->json([
            'success' => true,
            'message' => 'PDF berhasil dibuat & diunggah',
            'data'    => [
                'id'         => $draft->id,
                'file'       => $draft->file,
                'file_path'  => $draft->file_path,
                'updated_at' => $draft->updated_at,
            ],
        ], 200);
    }
}
