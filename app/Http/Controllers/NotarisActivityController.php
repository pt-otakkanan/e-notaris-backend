<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\User;
use App\Models\Activity;
use App\Models\Track;
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
     * GET /notaris/activity
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
            'track',
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
     * GET /notaris/activity/{id}
     */
    // App/Http/Controllers/NotarisActivityController.php
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::with([
            'deed.requirements',
            'notaris',
            'track',
            'clients' => function ($query) {
                $query->with('identity')
                    ->orderBy('client_activity.order', 'asc')   // <— pakai client_activity (singular)
                    ->orderBy('client_activity.id', 'asc');     // fallback
            },
            'clientActivities' => function ($query) {
                $query->orderBy('order', 'asc')                   // <— kolom order di pivot
                    ->orderBy('id', 'asc');                     // fallback
            },
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
     * POST /notaris/activity
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validasi = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'deed_id'      => 'required|exists:deeds,id',
            'client_ids'   => 'required|array|min:1',
            'client_ids.*' => 'required|integer|exists:users,id',
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

        // Validasi role penghadap
        $validIds = User::whereIn('id', $data['client_ids'])
            ->where('role_id', 2)
            ->pluck('id')
            ->all();

        if (count($validIds) !== count($data['client_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Semua klien harus memiliki role penghadap.',
                'data'    => null,
            ], 422);
        }

        $deed   = Deed::with('requirements')->find($data['deed_id']);
        // Susun kembali sesuai urutan FE (hanya id valid)
        $orderedClientIds = [];
        foreach ($data['client_ids'] as $cid) {
            if (in_array($cid, $validIds, true)) {
                $orderedClientIds[] = $cid;
            }
        }

        return DB::transaction(function () use ($user, $deed, $data, $orderedClientIds) {
            // 1) Buat Activity
            $activity = Activity::create([
                'name'                => $data['name'],
                'deed_id'             => $deed->id,
                'user_notaris_id'     => $user->id,
                'activity_notaris_id' => $user->id,
                'tracking_code'       => 'ACT-' . strtoupper(Str::random(8)),
            ]);

            // 2) Buat Track default & tautkan
            $track = Track::create([
                'status_invite'   => 'done',
                'status_respond'  => 'todo',
                'status_docs'     => 'pending',
                'status_draft'    => 'pending',
                'status_schedule' => 'pending',
                'status_sign'     => 'pending',
                'status_print'    => 'pending',
            ]);
            $activity->track_id = $track->id;
            $activity->save();

            // 3) Isi pivot client_activity + order
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

            // 4) Generate DocumentRequirement per client & requirement
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

            $activity->load([
                'deed.requirements',
                'notaris',
                'track',
                'clients' => function ($q) {
                    $q->orderBy('client_activity.order', 'asc');
                },
                'clientActivities' => function ($q) {
                    $q->orderBy('order', 'asc');
                },
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
     * POST /notaris/activity/update/{id}
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

        return DB::transaction(function () use ($data, $activity) {
            // Jika deed berubah, cek kebutuhan jumlah klien terhadap data yang akan dipakai
            if (isset($data['deed_id'])) {
                $deedNew = Deed::with('requirements')->find($data['deed_id']);

                // Tentukan calon daftar klien yang akan aktif setelah update
                if (isset($data['client_ids'])) {
                    $validIds = User::whereIn('id', $data['client_ids'])
                        ->where('role_id', 2)
                        ->pluck('id')
                        ->all();

                    // susun sesuai urutan FE
                    $orderedClientIds = [];
                    foreach ($data['client_ids'] as $cid) {
                        if (in_array($cid, $validIds, true)) {
                            $orderedClientIds[] = $cid;
                        }
                    }
                } else {
                    // pakai klien yang sudah ada sekarang (urut berdasar pivot.order)
                    $orderedClientIds = $activity->clientActivities()
                        ->orderBy('order', 'asc')
                        ->pluck('user_id')
                        ->all();
                }

                $activity->deed_id = $deedNew->id;
                $activity->save();
            }

            if (isset($data['name'])) {
                $activity->name = $data['name'];
                $activity->save();
            }

            // Jika client_ids dikirim → reset pivot & set order baru + regenerate documents
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

                // Validasi jumlah sesuai deed aktif saat ini
                $deed = $activity->deed()->first();
                
                // Reset pivot
                ClientActivity::where('activity_id', $activity->id)->delete();

                // Insert ulang pivot + order
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

                // Regenerasi DocumentRequirement
                DocumentRequirement::where('activity_notaris_id', $activity->id)->delete();
                $deed = $activity->deed()->with('requirements')->first();

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
     * DELETE /notaris/activity/{id}
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

        // ====== Proteksi penghapusan (silakan sesuaikan kebijakan) ======
        // Saat ini kamu blok kalau global status 'approved' (semua klien setuju)
        // atau ada salah satu klien 'approved'.
        // -> Jika ingin TETAP boleh menghapus walau track.status_respond = 'done',
        //    hapus/ubah blok ini sesuai kebutuhan.

        $hasApproved = $activity->clientActivities()
            ->where('status_approval', 'approved')
            ->exists();

        // Jika kamu ingin memblok hanya kalau SUDAH TANDA TANGAN / CETAK, ganti ceknya ke track:
        // $lockedByTrack = in_array(optional($activity->track)->status_sign, ['done'])
        //               || in_array(optional($activity->track)->status_print, ['done']);

        if ($activity->status_approval === 'approved' || $hasApproved) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas yang sudah disetujui tidak dapat dihapus.',
                'data'    => null
            ], 409);
        }
        // ===============================================================

        try {
            DB::beginTransaction();

            // simpan track_id untuk dihapus setelah activity hilang
            $trackId = $activity->track_id;

            // 1) Hapus anak-anaknya
            //    (kalau kamu perlu hapus file Cloudinary, lakukan di sini lebih dulu)
            $activity->documentRequirements()->delete();
            $activity->draftDeeds()->delete();
            $activity->schedules()->delete();
            $activity->clientActivities()->delete();

            // 2) Hapus activity TERLEBIH DULU
            $activity->delete();

            // 3) (Opsional) Hapus track setelah activity dihapus
            if ($trackId) {
                Track::where('id', $trackId)->delete();
                // Alternatif kalau FK dibuat set null:
                // Activity::where('id', $id)->update(['track_id' => null]);
                // Track::where('id', $trackId)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil dihapus',
                'data'    => null
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            // tampilkan pesan singkat agar FE enak dibaca
            $msg = method_exists($e, 'getMessage') ? $e->getMessage() : 'Terjadi kesalahan saat menghapus aktivitas.';
            return response()->json([
                'success' => false,
                'message' => $msg,
                'data'    => null
            ], 500);
        }
    }


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
     * GET /notaris/activity/user/client
     */
    public function getUsers(Request $request)
    {
        $search  = $request->query('search');

        $query = User::query()
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


    public function markDone($id)
    {

        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $activity->track()->update([
            'status_docs'  => 'done',
            'status_draft' => 'todo',
        ]);

        // ambil ulang data track yang fresh
        $activity->load('track');


        return response()->json([
            'success' => true,
            'message' => 'Status Doc aktivitas berhasil ditandai selesai cui',
            'data'    => $activity->track
        ], 200);
    }
}
