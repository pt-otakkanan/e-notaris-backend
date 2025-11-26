<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Requirement;              // requirement milik activity
use App\Models\DocumentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    /**
     * Buat / update baris DocumentRequirement untuk semua klien
     * di activity terkait requirement ini. Tidak menghapus nilai yang sudah diisi.
     */
    private function upsertDocReqForRequirement(Requirement $req): int
    {
        $act = Activity::with(['clients:id'])->find($req->activity_id);
        if (!$act) return 0;

        $clientIds = $act->clients->pluck('id')->all();
        // Jika tidak ada klien, tapi aktivitas bertanda is_without_client,
        // buat DocumentRequirement untuk notaris (owner) supaya notaris bisa mengisi dokumen.
        if (empty($clientIds)) {
            if ($act->is_without_client) {
                $clientIds = [$act->user_notaris_id];
            } else {
                return 0;
            }
        }

        $now  = now();
        $rows = [];
        foreach ($clientIds as $uid) {
            $rows[] = [
                'activity_notaris_id' => $act->id,
                'user_id'             => $uid,
                'requirement_id'      => $req->id,
                'requirement_name'    => $req->name,             // snapshot label
                'is_file_snapshot'    => (bool) $req->is_file,    // snapshot tipe
                // JANGAN isi value/file di upsert → agar tidak override data user
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        // upsert by unique (activity_notaris_id,user_id,requirement_id)
        // update hanya kolom snapshot & updated_at
        foreach (array_chunk($rows, 500) as $chunk) {
            DocumentRequirement::upsert(
                $chunk,
                ['activity_notaris_id', 'user_id', 'requirement_id'],
                ['requirement_name', 'is_file_snapshot', 'updated_at']
            );
        }

        return count($rows);
    }

    /**
     * GET /requirements
     * Query: activity_id (disarankan), search, per_page
     */
    public function index(Request $request)
    {
        $q          = $request->query('search');
        $activityId = $request->query('activity_id');   // ⬅️ filter
        $perPage    = max(1, (int) ($request->query('per_page', 10)));

        $query = Requirement::query()->with('activity:id,name');

        if ($activityId) {
            $query->where('activity_id', $activityId);
        }

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $requirements = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar persyaratan berhasil diambil.',
            'data'    => $requirements->items(),
            'meta'    => [
                'current_page' => $requirements->currentPage(),
                'per_page'     => $requirements->perPage(),
                'total'        => $requirements->total(),
                'last_page'    => $requirements->lastPage(),
            ]
        ], 200);
    }

    /**
     * GET /requirements/{id}
     */
    public function show($id)
    {
        $requirement = Requirement::with('activity:id,name')->find($id);

        if (!$requirement) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail persyaratan berhasil diambil.',
            'data'    => $requirement
        ], 200);
    }

    /**
     * POST /requirements
     * Body: activity_id, name, is_file
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'activity_id' => 'required|integer|exists:activity,id',
            'name'        => 'required|string|max:255',
            'is_file'     => 'required|boolean',
        ], [
            'activity_id.required' => 'Aktivitas wajib diisi.',
            'activity_id.integer'  => 'ID aktivitas harus berupa angka.',
            'activity_id.exists'   => 'Aktivitas tidak ditemukan.',
            'name.required'        => 'Nama persyaratan wajib diisi.',
            'name.string'          => 'Nama persyaratan harus berupa teks.',
            'name.max'             => 'Nama persyaratan maksimal 255 karakter.',
            'is_file.required'     => 'Tipe persyaratan wajib diisi.',
            'is_file.boolean'      => 'Tipe persyaratan harus berupa true/false.',
        ]);

        $validasi->setAttributeNames([
            'activity_id' => 'Aktivitas',
            'name'        => 'Nama Persyaratan',
            'is_file'     => 'Tipe Persyaratan',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal.',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $req = DB::transaction(function () use ($validasi) {
            $req = Requirement::create($validasi->validated());
            // buat/refresh baris DocumentRequirement untuk semua klien activity ini
            $this->upsertDocReqForRequirement($req);
            return $req;
        });

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil dibuat.',
            'data'    => $req,
        ], 201);
    }

    /**
     * PUT /requirements/{id}
     * Body: name?, is_file?
     * (Tidak mengizinkan pindah activity_id di sini agar aman)
     */
    public function update(Request $request, $id)
    {
        $req = Requirement::find($id);
        if (!$req) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'    => 'sometimes|required|string|max:255',
            'is_file' => 'sometimes|required|boolean',
            // jika ingin izinkan pindah activity:
            // 'activity_id' => 'sometimes|required|integer|exists:activities,id'
        ], [
            'name.required'    => 'Nama persyaratan wajib diisi.',
            'name.string'      => 'Nama persyaratan harus berupa teks.',
            'name.max'         => 'Nama persyaratan maksimal 255 karakter.',
            'is_file.required' => 'Tipe persyaratan wajib diisi.',
            'is_file.boolean'  => 'Tipe persyaratan harus berupa true/false.',
        ]);

        $validasi->setAttributeNames([
            'name'    => 'Nama Persyaratan',
            'is_file' => 'Tipe Persyaratan',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal.',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        DB::transaction(function () use ($req, $data) {
            // jangan izinkan perubahan activity_id di route ini (opsional safety)
            unset($data['activity_id']);

            $req->fill($data);
            $req->save();

            // refresh snapshot di document_requirements (nama / tipe file)
            $this->upsertDocReqForRequirement($req);
        });

        $req->load('activity:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil diperbarui.',
            'data'    => $req
        ], 200);
    }

    /**
     * DELETE /requirements/{id}
     * Diblok jika requirement ini sudah dipakai/approved.
     */
    public function destroy($id)
    {
        $req = Requirement::find($id);
        if (!$req) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        $usedCount = DocumentRequirement::where('requirement_id', $req->id)
            ->where(function ($q) {
                $q->whereNotNull('value')
                    ->orWhereNotNull('file')
                    ->orWhere('status_approval', 'approved');
            })
            ->count();

        if ($usedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus: persyaratan sudah dipakai atau telah disetujui.',
                'data'    => null
            ], 422);
        }

        DB::transaction(function () use ($req) {
            // hapus doc-req kosong untuk requirement ini (kalau masih ada)
            DocumentRequirement::where('requirement_id', $req->id)->delete();
            $req->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil dihapus.',
            'data'    => null
        ], 200);
    }
}
