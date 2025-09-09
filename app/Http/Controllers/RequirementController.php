<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\User;
use App\Models\Track;
use App\Models\Activity;
use App\Models\Schedule;
use App\Models\Requirement;
use Illuminate\Http\Request;
use App\Models\ClientActivity;
use App\Models\DocumentRequirement;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\DB;

class RequirementController extends Controller
{

    private function syncRequirementToActivities(Requirement $req): int
    {
        // Ambil semua activity yang memakai deed ini, beserta clients-nya
        $activities = Activity::with(['clients:id'])
            ->where('deed_id', $req->deed_id)
            ->get(['id']);

        if ($activities->isEmpty()) return 0;

        $now  = now();
        $rows = [];

        /** @var Activity $act */
        foreach ($activities as $act) {
            foreach ($act->clients as $client) {
                $rows[] = [
                    'activity_notaris_id' => $act->id,
                    'user_id'             => $client->id,
                    'requirement_id'      => $req->id,
                    'requirement_name'    => $req->name,           // snapshot label
                    'is_file_snapshot'    => (bool) $req->is_file, // snapshot tipe
                    'status_approval'     => 'pending',
                    'value'               => null,                 // biarkan null; kalau sudah ada, upsert akan update kolom di bawah saja
                    'file'                => null,
                    'file_path'           => null,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
            }
        }

        if (empty($rows)) return 0;

        // Upsert per chunk agar tidak berat (butuh unique index)
        $inserted = 0;
        foreach (array_chunk($rows, 500) as $chunk) {
            DocumentRequirement::upsert(
                $chunk,
                ['activity_notaris_id', 'user_id', 'requirement_id'], // unique by
                ['requirement_name', 'is_file_snapshot', 'updated_at'] // kolom yang di-update on conflict
            );
            $inserted += count($chunk);
        }

        return $inserted;
    }
    /**
     * GET /requirements
     * Opsional: filter by deed_id, search, pagination
     */
    public function index(Request $request)
    {
        $q        = $request->query('search');
        $deedId   = $request->query('deed_id');
        $perPage  = (int)($request->query('per_page', 10));
        $perPage  = $perPage > 0 ? $perPage : 10;

        $query = Requirement::query()->with('deed');

        if ($deedId) {
            $query->where('deed_id', $deedId);
        }

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $requirements = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar persyaratan berhasil diambil',
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
        $requirement = Requirement::with('deed')->find($id);

        if (!$requirement) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail persyaratan berhasil diambil',
            'data'    => $requirement
        ], 200);
    }

    /**
     * POST /requirements
     * Body: deed_id, name, is_file
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'deed_id' => 'required|exists:deeds,id',
            'name'    => 'required|string|max:255',
            'is_file' => 'required|boolean',
        ], [ /* ...messages... */]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $requirement = DB::transaction(function () use ($validasi) {
            $req = Requirement::create($validasi->validated());
            // sinkron ke semua activity yang pakai deed ini
            $this->syncRequirementToActivities($req);
            return $req;
        });

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil dibuat',
            'data'    => $requirement,
        ], 201);
    }


    /**
     * PUT /requirements/{id}
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::where('id', $id)
            ->where('user_notaris_id', $user->id)
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'         => 'sometimes|required|string|max:255',
            'deed_id'      => 'sometimes|required|exists:deeds,id',
            'client_ids'   => 'sometimes|required|array|min:1',
            'client_ids.*' => 'required|integer|exists:users,id',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        // ←— RULE BARU: deed_id hanya boleh diubah kalau BELUM ada approval
        $wantChangeDeed = isset($data['deed_id']) && (int)$data['deed_id'] !== (int)$activity->deed_id;
        if ($wantChangeDeed) {
            $hasApproved = $activity->clientActivities()
                ->where('status_approval', 'approved')
                ->exists();

            if ($hasApproved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah jenis akta karena sudah ada penghadap yang menyetujui undangan.',
                    'data'    => null,
                ], 422);
            }
        }

        return DB::transaction(function () use ($data, $activity, $wantChangeDeed) {

            // 1) Update name (opsional)
            if (isset($data['name'])) {
                $activity->name = $data['name'];
                $activity->save();
            }

            // 2) Tentukan daftar klien target (untuk regen dokumen)
            if (isset($data['client_ids'])) {
                $validIds = User::whereIn('id', $data['client_ids'])
                    ->where('role_id', 2)
                    ->pluck('id')
                    ->all();

                $orderedClientIds = [];
                foreach ($data['client_ids'] as $cid) {
                    if (in_array($cid, $validIds, true)) {
                        $orderedClientIds[] = $cid;
                    }
                }

                // Reset pivot & order baru
                ClientActivity::where('activity_id', $activity->id)->delete();

                $now  = now();
                $rows = [];
                $ord  = 1;
                foreach ($orderedClientIds as $uid) {
                    $rows[] = [
                        'user_id'         => $uid,
                        'activity_id'     => $activity->id,
                        'status_approval' => 'pending',
                        'order'           => $ord++,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
                ClientActivity::insert($rows);
            } else {
                // kalau client_ids tidak dikirim → pakai klien existing
                $orderedClientIds = $activity->clientActivities()
                    ->orderBy('order', 'asc')
                    ->pluck('user_id')
                    ->all();
            }

            // 3) Jika deed berubah (dan lolos guard), set dan REGENERATE document_requirements
            if ($wantChangeDeed) {
                $deedNew = Deed::with('requirements')->find($data['deed_id']);
                $activity->deed_id = $deedNew->id;
                $activity->save();

                // Reset dokumen lama & generate ulang sesuai deed baru untuk klien aktif
                DocumentRequirement::where('activity_notaris_id', $activity->id)->delete();

                $now     = now();
                $docRows = [];
                foreach ($deedNew->requirements as $req) {
                    foreach ($orderedClientIds as $uid) {
                        $docRows[] = [
                            'activity_notaris_id' => $activity->id,
                            'user_id'             => $uid,
                            'requirement_id'      => $req->id,
                            'requirement_name'    => $req->name,
                            'is_file_snapshot'    => (bool) $req->is_file,
                            'value'               => null,
                            'file'                => null,
                            'file_path'           => null,
                            'status_approval'     => 'pending',
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ];
                    }
                }
                if (!empty($docRows)) {
                    DocumentRequirement::insert($docRows);
                }
            }

            // 4) Jika hanya ganti klien (deed tidak berubah), sinkron dokumen terhadap deed aktif saat ini
            if (isset($data['client_ids']) && !$wantChangeDeed) {
                $deed = $activity->deed()->with('requirements')->first();

                DocumentRequirement::where('activity_notaris_id', $activity->id)->delete();

                $now     = now();
                $docRows = [];
                foreach ($deed->requirements as $req) {
                    foreach ($orderedClientIds as $uid) {
                        $docRows[] = [
                            'activity_notaris_id' => $activity->id,
                            'user_id'             => $uid,
                            'requirement_id'      => $req->id,
                            'requirement_name'    => $req->name,
                            'is_file_snapshot'    => (bool) $req->is_file,
                            'value'               => null,
                            'file'                => null,
                            'file_path'           => null,
                            'status_approval'     => 'pending',
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ];
                    }
                }
                if (!empty($docRows)) {
                    DocumentRequirement::insert($docRows);
                }
            }

            // 5) Return detail terbaru
            $activity->load([
                'deed',
                'notaris',
                'track',
                'clients' => function ($q) {
                    $q->orderBy('client_activity.order', 'asc');
                },
                'clientActivities' => function ($q) {
                    $q->orderBy('order', 'asc');
                },
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil diperbarui',
                'data'    => $activity
            ], 200);
        });
    }

    /**
     * DELETE /requirements/{id}
     */
    public function destroy($id)
    {
        $requirement = Requirement::find($id);
        if (!$requirement) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // Cek pemakaian
        $usedCount = DocumentRequirement::where('requirement_id', $requirement->id)
            ->where(function ($q) {
                $q->whereNotNull('value')
                    ->orWhereNotNull('file')
                    ->orWhere('status_approval', 'approved');
            })->count();

        if ($usedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus: persyaratan sudah dipakai/diapprove.',
                'data'    => null
            ], 422);
        }

        DB::transaction(function () use ($requirement) {
            DocumentRequirement::where('requirement_id', $requirement->id)->delete();
            $requirement->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil dihapus',
            'data'    => null
        ], 200);
    }
}
