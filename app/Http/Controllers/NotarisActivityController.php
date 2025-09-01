<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\User;
use App\Models\Activity;
use App\Models\Track;                 // ⬅️ tambahkan
use App\Models\ClientActivity;
use App\Models\DocumentRequirement;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class NotarisActivityController extends Controller
{
    
    /**
     * GET /activities
     * Query: search, status (pending|approved|rejected), per_page
     */
    public function index(Request $request)
    {
        $user           = $request->user();
        $search         = $request->query('search');
        $approvalStatus = $request->query('status');
        $perPage        = (int)($request->query('per_page', 10)) ?: 10;

        $query = Activity::with([
            'deed',
            'notaris',
            'track',                       // tetap load track
            'clients:id,name,email',
            'clientActivities',
            'schedules'
        ])->where('user_notaris_id', $user->id);

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('tracking_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('deed', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($approvalStatus && in_array($approvalStatus, ['pending', 'approved', 'rejected'], true)) {
            $query->where(function ($q) use ($approvalStatus) {
                if ($approvalStatus === 'approved') {
                    $q->whereDoesntHave('clientActivities', function ($h) {
                        $h->where('status_approval', '!=', 'approved');
                    });
                } elseif ($approvalStatus === 'rejected') {
                    $q->whereHas('clientActivities', function ($h) {
                        $h->where('status_approval', 'rejected');
                    });
                } else { // pending
                    $q->whereHas('clientActivities', function ($h) {
                        $h->where('status_approval', 'pending');
                    })->whereDoesntHave('clientActivities', function ($h) {
                        $h->where('status_approval', 'rejected');
                    });
                }
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar aktivitas berhasil diambil',
            'data'    => $activities->items(),
            'meta'    => [
                'current_page' => $activities->currentPage(),
                'per_page'     => $activities->perPage(),
                'total'        => $activities->total(),
                'last_page'    => $activities->lastPage(),
                'from'         => $activities->firstItem(),
                'to'           => $activities->lastItem(),
            ]
        ], 200);
    }

    /**
     * GET /activities/{id}
     * Akses: notaris pemilik, atau user yang termasuk di client_activity
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::with([
            'deed.requirements',
            'notaris',
            'track',
            'clients.identity',
            'clientActivities',
            'schedules'
        ])
            ->where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_notaris_id', $user->id)
                    ->orWhereHas('clients', function ($c) use ($user) {
                        $c->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail aktivitas berhasil diambil',
            'data'    => $activity
        ], 200);
    }

    /**
     * POST /activities
     * Body:
     * - name (string)
     * - deed_id (exists:deeds,id)
     * - client_ids (array of user id role_id=2) — jumlah HARUS = deeds.total_client
     * (track dibuat OTOMATIS)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validasi = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'deed_id'       => 'required|exists:deeds,id',
            'client_ids'    => 'required|array|min:1',
            'client_ids.*'  => 'required|integer|exists:users,id',
        ], [
            'client_ids.required' => 'Daftar klien wajib diisi.',
            'client_ids.array'    => 'Format daftar klien tidak valid.',
            'client_ids.min'      => 'Minimal 1 klien.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        // Validasi role client = penghadap
        $clients = User::whereIn('id', $data['client_ids'])
            ->where('role_id', 2)
            ->pluck('id')
            ->all();

        if (count($clients) !== count($data['client_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Semua klien harus memiliki role penghadap.',
                'data'    => null,
            ], 422);
        }

        // Cek kebutuhan jumlah klien sesuai deed->total_client
        $deed   = Deed::with('requirements')->find($data['deed_id']);
        $needed = (int) $deed->total_client;

        if (count($clients) !== $needed) {
            return response()->json([
                'success' => false,
                'message' => "Akta ini memerlukan {$needed} klien.",
                'data'    => null,
            ], 422);
        }

        return DB::transaction(function () use ($user, $deed, $data, $clients) {
            // 1) Buat Activity
            $activity = Activity::create([
                'name'                => $data['name'],
                'deed_id'             => $deed->id,
                'user_notaris_id'     => $user->id,
                'activity_notaris_id' => $user->id,
                'tracking_code'       => 'ACT-' . strtoupper(Str::random(8)),
                'status_approval'     => 'pending',
            ]);

            // 2) Buat Track default dan tautkan ke Activity
            $track = Track::create([
                'status_invite'   => 'done',
                'status_respond'  => 'pending',
                'status_docs'     => 'pending',
                'status_draft'    => 'pending',
                'status_schedule' => 'pending',
                'status_sign'     => 'pending',
                'status_print'    => 'pending',
            ]);
            $activity->track_id = $track->id;
            $activity->save();

            // 3) Isi pivot client_activity (pending)
            $now  = now();
            $rows = [];
            foreach ($clients as $uid) {
                $rows[] = [
                    'user_id'         => $uid,
                    'activity_id'     => $activity->id,
                    'status_approval' => 'pending',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
            ClientActivity::insert($rows);

            // 4) Generate DocumentRequirement per client & requirement
            $docRows = [];
            foreach ($deed->requirements as $req) {
                foreach ($clients as $uid) {
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

            $activity->load([
                'deed.requirements',
                'notaris',
                'track',
                'clients',
                'clientActivities',
                'schedules',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas & dokumen persyaratan berhasil dibuat',
                'data'    => $activity,
            ], 201);
        });
    }

    /**
     * PUT /activities/{id}
     * Dapat mengubah: name, deed_id, client_ids, status_approval
     * (track_id TIDAK bisa diubah lewat endpoint ini)
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
            'name'            => 'sometimes|required|string|max:255',
            'deed_id'         => 'sometimes|required|exists:deeds,id',
            'client_ids'      => 'sometimes|required|array|min:1',
            'client_ids.*'    => 'required|integer|exists:users,id',
            'status_approval' => 'sometimes|required|in:pending,approved,rejected',
            // note: tidak ada track_id di sini
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        return DB::transaction(function () use ($data, $activity) {

            // Jika deed berubah, validasi jumlah client sesuai deed baru
            if (isset($data['deed_id'])) {
                $deed = Deed::with('requirements')->find($data['deed_id']);
                $currentClientIds = $activity->clients()->pluck('users.id')->all();

                // Jika client_ids ikut dikirim, gunakan itu; jika tidak, pakai klien yang sudah ada
                if (isset($data['client_ids'])) {
                    $newClients = User::whereIn('id', $data['client_ids'])
                        ->where('role_id', 2)
                        ->pluck('id')->all();
                } else {
                    $newClients = $currentClientIds;
                }

                $needed = (int) $deed->total_client;
                if (count($newClients) !== $needed) {
                    abort(response()->json([
                        'success' => false,
                        'message' => "Akta ini memerlukan {$needed} klien.",
                        'data'    => null,
                    ], 422));
                }

                $activity->deed_id = $deed->id;
            }

            if (isset($data['name']))            $activity->name = $data['name'];
            if (isset($data['status_approval'])) $activity->status_approval = $data['status_approval'];

            $activity->save();

            // Sinkronisasi klien jika client_ids dikirim
            if (isset($data['client_ids'])) {
                $clientIds = User::whereIn('id', $data['client_ids'])
                    ->where('role_id', 2)
                    ->pluck('id')->all();

                $deed   = $activity->deed()->first();
                $needed = (int) $deed->total_client;
                if (count($clientIds) !== $needed) {
                    abort(response()->json([
                        'success' => false,
                        'message' => "Akta ini memerlukan {$needed} klien.",
                        'data'    => null,
                    ], 422));
                }

                // Reset pivot
                ClientActivity::where('activity_id', $activity->id)->delete();

                $now  = now();
                $rows = [];
                foreach ($clientIds as $uid) {
                    $rows[] = [
                        'user_id'         => $uid,
                        'activity_id'     => $activity->id,
                        'status_approval' => 'pending',
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
                ClientActivity::insert($rows);

                // Regenerasi DocumentRequirement agar sinkron
                DocumentRequirement::where('activity_notaris_id', $activity->id)->delete();
                $deed = $activity->deed()->with('requirements')->first();
                $docRows = [];
                foreach ($deed->requirements as $req) {
                    foreach ($clientIds as $uid) {
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

            $activity->load(['deed', 'notaris', 'track', 'clients', 'clientActivities']);

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil diperbarui',
                'data'    => $activity
            ], 200);
        });
    }

    /**
     * DELETE /activities/{id}
     */
    public function destroy(Request $request, $id)
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

        $hasApproved = $activity->clientActivities()
            ->where('status_approval', 'approved')
            ->exists();

        if ($activity->status_approval === 'approved' || $hasApproved) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas yang sudah disetujui tidak dapat dihapus.',
                'data'    => null
            ], 409);
        }

        try {
            DB::beginTransaction();

            $activity->documentRequirements()
                ->select(['id', 'file_path'])
                ->chunkById(200, function ($docs) {
                    foreach ($docs as $doc) {
                        if (!empty($doc->file_path)) {
                            try {
                                Cloudinary::destroy($doc->file_path);
                            } catch (\Throwable $e) {
                            }
                        }
                        DocumentRequirement::where('id', $doc->id)->delete();
                    }
                });

            $activity->draftDeeds()->delete();
            $activity->schedules()->delete();
            $activity->clientActivities()->delete();

            // (opsional) ikut hapus track terkait agar rapi
            if ($activity->track_id) {
                Track::where('id', $activity->track_id)->delete();
            }

            $activity->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas dan seluruh data terkait berhasil dihapus',
                'data'    => null
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus aktivitas.',
                'data'    => null
            ], 500);
        }
    }

    /**
     * PUT /activities/{id}/approve
     * (tetap ada status global activity)
     */
    public function approve($id)
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $activity->status_approval = 'approved';
        $activity->save();

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil disetujui',
            'data'    => $activity
        ], 200);
    }

    /**
     * PUT /activities/{id}/reject
     */
    public function reject($id)
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $activity->status_approval = 'rejected';
        $activity->save();

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil ditolak',
            'data'    => $activity
        ], 200);
    }

    /**
     * GET /activities/users
     * Ambil daftar calon klien (role penghadap & approved)
     */
    public function getUsers(Request $request)
    {
        $search  = $request->query('search');

        $query = \App\Models\User::query()
            ->select(['id', 'name', 'email', 'telepon', 'gender', 'status_verification', 'role_id'])
            ->with(['identity:id,user_id,file_photo'])
            ->where('role_id', 2)
            ->where('status_verification', 'approved');

        if ($search) {
            $query->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        $options = $users->map(function ($u) {
            return [
                'value'  => $u->id,
                'label'  => $u->name . ' (' . $u->email . ')',
                'name'   => $u->name,
                'email'  => $u->email,
                'avatar' => optional($u->identity)->file_photo,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar klien terverifikasi berhasil diambil',
            'data'    => $options,
        ], 200);
    }
}
