<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\User;
use App\Models\Track;
use App\Models\Activity;
use App\Models\DraftDeed;
use App\Models\ClientDraft;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ClientActivity;
use App\Mail\ClientActivityMail;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentRequirement;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Requirement;              // requirement milik activity

class NotarisActivityController extends Controller
{
    private function buildActivityMailDetails(User $client, Activity $activity, string $type): array
    {
        // asumsi relasi: Activity belongsTo User (notaris) via user_notaris_id
        $notary = User::find($activity->user_notaris_id);

        // ambil jadwal pertama jika ada (opsional, sesuaikan dengan struktur kamu)
        $schedule = $activity->schedules[0] ?? null; // kalau punya relasi schedules
        $place    = $schedule->place ?? ($activity->notaris->city ?? null) ?? null;

        // tanggal human readable (opsional)
        $dateStr  = null;
        if (!empty($schedule?->datetime)) {
            try {
                $dateStr = \Carbon\Carbon::parse($schedule->datetime)->locale('id')->translatedFormat('d M Y, H:i');
            } catch (\Throwable $e) {
                $dateStr = null;
            }
        }

        $frontend = rtrim(config('app.frontend_url'), '/');
        $url = $frontend . '/app/activity/' . $activity->id;

        return [
            'type'          => $type, // 'added' | 'removed'
            'subject'       => $type === 'added'
                ? 'Anda Ditambahkan ke Aktivitas'
                : 'Anda Dihapus dari Aktivitas',
            'app_name'      => config('app.name'),
            'client_name'   => $client->name,
            'client_email'  => $client->email,
            'activity_id'   => $activity->id,
            'activity_name' => $activity->name ?? 'Aktivitas',
            'tracking_code' => $activity->tracking_code ?? '-',
            'notary_name'   => $notary?->name ?? '-',
            'place'         => $place,
            'date_str'      => $dateStr,
            'url'           => $url,
        ];
    }

    /**
     * Kirim email notifikasi ke klien (dan BCC ke notaris) setelah commit transaksi.
     */
    private function notifyClientActivity(User $client, Activity $activity, string $type): void
    {
        $details = $this->buildActivityMailDetails($client, $activity, $type);
        $subject = $details['subject'] . ' - ' . ($details['activity_name'] ?? 'Aktivitas');

        DB::afterCommit(function () use ($client, $activity, $details, $subject) {
            try {
                $notary = User::find($activity->user_notaris_id);

                $mailable = new ClientActivityMail($details, $subject);

                // kirim ke klien; BCC ke notaris (opsional — boleh dihapus jika tak perlu)
                $mailer = Mail::to($client->email, $client->name);
                if ($notary?->email) {
                    $mailer->bcc($notary->email, $notary->name);
                }
                $mailer->send($mailable);
            } catch (\Throwable $e) {
                // user sebelumnya prefer tidak menggunakan \Log
                // bisa diabaikan agar tidak mengganggu flow utama
            }
        });
    }

    // ---------- ADD USER (dengan email) ----------

    public function addUser(Request $request, $userid, $activityid)
    {
        $user = $request->user();

        $activity = Activity::where('id', $activityid)
            ->where('user_notaris_id', $user->id)
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $client = User::where('id', $userid)
            ->where('role_id', 2)
            ->where('status_verification', 'approved')
            ->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Klien tidak valid atau belum terverifikasi',
                'data'    => null
            ], 422);
        }

        $exists = ClientActivity::where('activity_id', $activityid)
            ->where('user_id', $userid)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Klien sudah terdaftar pada aktivitas ini',
                'data'    => null
            ], 409);
        }

        $lastOrder = ClientActivity::where('activity_id', $activityid)->max('order') ?? 0;
        $now = now();

        DB::transaction(function () use ($activity, $userid, $lastOrder, $now) {
            // 1) Tambah pivot
            ClientActivity::create([
                'user_id'         => $userid,
                'activity_id'     => $activity->id,
                'status_approval' => 'pending',
                'order'           => $lastOrder + 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            // 2) Ambil requirement milik ACTIVITY
            $actReq = Requirement::where('activity_id', $activity->id)->get();

            // 3) Generate DocumentRequirement utk user baru
            if ($actReq->count()) {
                $docRows = [];
                foreach ($actReq as $req) {
                    $docRows[] = [
                        'activity_notaris_id' => $activity->id,
                        'user_id'             => $userid,
                        'requirement_id'      => $req->id,
                        'requirement_name'    => $req->name,
                        'is_file_snapshot'    => (bool)$req->is_file,
                        'value'               => null,
                        'file'                => null,
                        'file_path'           => null,
                        'status_approval'     => 'pending',
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
                DocumentRequirement::insert($docRows);
            }
        });

        // Email — kirim setelah commit
        $this->notifyClientActivity($client, $activity, 'added');

        return response()->json([
            'success' => true,
            'message' => 'Klien berhasil ditambahkan ke aktivitas'
        ], 201);
    }

    // ---------- REMOVE USER (dengan email) ----------

    public function removeUser(Request $request, $userid, $activityid)
    {
        $user = $request->user();

        $activity = Activity::where('id', $activityid)
            ->where('user_notaris_id', $user->id)
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $clientActivity = ClientActivity::where('activity_id', $activityid)
            ->where('user_id', $userid)
            ->first();

        if (!$clientActivity) {
            return response()->json([
                'success' => false,
                'message' => 'Klien tidak ditemukan pada aktivitas ini',
                'data'    => null
            ], 404);
        }

        if ($clientActivity->status_approval === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Klien yang sudah menyetujui tidak dapat dihapus.',
                'data'    => null
            ], 409);
        }

        DB::transaction(function () use ($activityid, $userid) {
            DocumentRequirement::where('activity_notaris_id', $activityid)
                ->where('user_id', $userid)
                ->delete();

            ClientActivity::where('activity_id', $activityid)
                ->where('user_id', $userid)
                ->delete();
        });

        // Ambil data klien untuk email
        $client = User::find($userid);
        if ($client) {
            $this->notifyClientActivity($client, $activity, 'removed');
        }

        return response()->json([
            'success' => true,
            'message' => 'Klien berhasil dihapus dari aktivitas',
            'data'    => null
        ], 200);
    }


    // App\Http\Controllers\NotarisActivityController.php

    public function index(Request $request)
    {
        $user           = $request->user();
        $search         = $request->query('search');
        $approvalStatus = $request->query('status');
        $perPage        = (int)($request->query('per_page', 10)) ?: 10;

        // Opsional: khusus admin, bisa filter by notaris_id
        $filterNotarisId = $request->query('notaris_id');

        $query = Activity::with([
            'deed',
            'notaris',
            'track',
            'clients:id,name,email',
            'clientActivities',
            'schedules'
        ]);

        if ((int)$user->role_id !== 1) {
            // NOTARIS: hanya miliknya sendiri
            $query->where('user_notaris_id', $user->id);
        } else {
            // ADMIN: lihat semua KECUALI proyek admin sendiri
            $query->where('user_notaris_id', '!=', $user->id);

            // Jika admin memberi filter notaris_id, terapkan juga (tetap exclude own)
            if (!empty($filterNotarisId)) {
                $query->where('user_notaris_id', (int)$filterNotarisId);
            }
        }

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
                } else {
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
     * Admin-only: daftar proyek milik admin sendiri (user_notaris_id = admin.id)
     */
    public function indexAdmin(Request $request)
    {
        $user = $request->user();

        if ((int)$user->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengakses.',
            ], 403);
        }

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
                } else {
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
            'message' => 'Daftar aktivitas (milik admin) berhasil diambil',
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



    public function show(Request $request, $id)
    {
        $user = $request->user();

        $baseWith = [
            'deed',
            'requirements',
            'documentRequirements',
            'notaris.identity',
            'track',
            'clients' => function ($query) {
                $query->with('identity')
                    ->orderBy('client_activity.order', 'asc')
                    ->orderBy('client_activity.id', 'asc');
            },
            'clientActivities' => function ($query) {
                $query->orderBy('order', 'asc')
                    ->orderBy('id', 'asc');
            },
            'schedules',
            'draft.clientDrafts.user'
        ];

        $query = Activity::with($baseWith)->where('id', $id);

        if ((int)$user->role_id !== 1) {
            // BUKAN admin → wajib notaris pemilik atau klien yang terlibat
            $query->where(function ($q) use ($user) {
                $q->where('user_notaris_id', $user->id)
                    ->orWhereHas('clients', function ($c) use ($user) {
                        $c->where('users.id', $user->id);
                    });
            });
        }
        // Admin tidak dipersempit

        $activity = $query->first();

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


    public function store(Request $request)
    {
        $user = $request->user();

        $validasi = Validator::make($request->all(), [
            'name'                   => 'required|string|max:255',
            'deed_id'                => 'required|exists:deeds,id',
            'client_ids'             => 'required|array|min:1',
            'client_ids.*'           => 'required|integer|exists:users,id',
            'requirements'           => 'sometimes|array',
            'requirements.*.name'    => 'required_with:requirements|string|max:255',
            'requirements.*.is_file' => 'required_with:requirements|boolean',
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

        // Urut sesuai FE
        $orderedClientIds = [];
        foreach ($data['client_ids'] as $cid) {
            if (in_array($cid, $validIds, true)) $orderedClientIds[] = $cid;
        }

        $deed = Deed::find($data['deed_id']);

        // ===== Transaksi: buat activity + semua turunannya =====
        [$activity, $createdDraftId] = DB::transaction(function () use ($user, $deed, $data, $orderedClientIds) {
            $now = now();

            // 1) Activity
            $activity = Activity::create([
                'name'                => $data['name'],
                'deed_id'             => $deed->id,
                'user_notaris_id'     => $user->id,
                'activity_notaris_id' => $user->id,
                'tracking_code'       => 'ACT-' . strtoupper(Str::random(8)),
            ]);

            // 2) Track
            $track = Track::create([
                'status_invite'   => 'done',
                'status_respond'  => 'todo',
                'status_docs'     => 'pending',
                'status_draft'    => 'pending',
                'status_schedule' => 'pending',
                'status_sign'     => 'pending',
                'status_print'    => 'pending',
            ]);
            $activity->update(['track_id' => $track->id]);

            // 3) Pivot client_activity
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

            // 4) Requirement awal milik activity (opsional)
            if (!empty($data['requirements']) && is_array($data['requirements'])) {
                $reqRows = [];
                foreach ($data['requirements'] as $r) {
                    $reqRows[] = [
                        'activity_id' => $activity->id,
                        'name'        => $r['name'],
                        'is_file'     => (bool)$r['is_file'],
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }
                Requirement::insert($reqRows);
            }

            // 5) Generate DocumentRequirement per klien
            $actReq = Requirement::where('activity_id', $activity->id)->get();
            if ($actReq->count() && !empty($orderedClientIds)) {
                $docRows = [];
                foreach ($actReq as $req) {
                    foreach ($orderedClientIds as $uid) {
                        $docRows[] = [
                            'activity_notaris_id' => $activity->id,
                            'user_id'             => $uid,
                            'requirement_id'      => $req->id,
                            'requirement_name'    => $req->name,
                            'is_file_snapshot'    => (bool)$req->is_file,
                            'value'               => null,
                            'file'                => null,
                            'file_path'           => null,
                            'status_approval'     => 'pending',
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ];
                    }
                }
                DocumentRequirement::insert($docRows);
            }

            // 6) Draft & ClientDrafts
            $draft = DraftDeed::create([
                'activity_id'           => $activity->id,
                'reference_number'      => now()->format('md') . '/OK/' . $activity->tracking_code . '/' . now()->format('Y'),
                'custom_value_template' => null,
                'reading_schedule'      => null,
                'status_approval'       => 'pending',
                'file'                  => null,
                'file_path'             => null,
            ]);

            if (!empty($orderedClientIds)) {
                $cdRows = [];
                foreach ($orderedClientIds as $uid) {
                    $cdRows[] = [
                        'user_id'         => $uid,
                        'draft_deed_id'   => $draft->id,
                        'status_approval' => 'pending',
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
                ClientDraft::insert($cdRows);
            }

            return [$activity, $draft->id];
        });

        // Eager load untuk response
        $activity->load([
            'deed',
            'notaris',
            'track',
            'clients' => fn($q) => $q->orderBy('client_activity.order', 'asc'),
            'clientActivities' => fn($q) => $q->orderBy('order', 'asc'),
            'requirements',
            'documentRequirements',
            'draft.clientDrafts.user',
            'schedules',
        ]);

        // ===== Kirim email "added" ke semua klien, setelah commit =====
        $clientUsers = User::whereIn('id', $orderedClientIds)->get();
        DB::afterCommit(function () use ($clientUsers, $activity) {
            foreach ($clientUsers as $client) {
                try {
                    $this->notifyClientActivity($client, $activity, 'added');
                } catch (\Throwable $e) {
                    // sengaja diamkan (sesuai preferensimu tidak pakai \Log)
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas, persyaratan (activity), dan draft berhasil dibuat',
            'data'    => $activity,
        ], 201);
    }

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
        $now  = now();

        // ===== Jalankan transaksi dan kembalikan daftar perubahan untuk email =====
        [$activity, $toAdd, $actuallyRemoved] = DB::transaction(function () use ($data, $activity, $now) {

            // 1) Tentukan target klien (urutan sesuai input FE)
            if (isset($data['client_ids'])) {
                $validIds = User::whereIn('id', $data['client_ids'])
                    ->where('role_id', 2)
                    ->pluck('id')
                    ->all();

                $orderedClientIds = [];
                foreach ($data['client_ids'] as $cid) {
                    if (in_array($cid, $validIds, true)) $orderedClientIds[] = $cid;
                }

                if (empty($orderedClientIds)) {
                    throw new \Illuminate\Validation\ValidationException(
                        Validator::make([], []),
                        response()->json([
                            'success' => false,
                            'message' => 'Daftar klien tidak valid.',
                            'data'    => null,
                        ], 422)
                    );
                }
            } else {
                $orderedClientIds = $activity->clientActivities()
                    ->orderBy('order', 'asc')
                    ->pluck('user_id')
                    ->all();
            }

            // 2) Update nama
            if (isset($data['name'])) {
                $activity->name = $data['name'];
                $activity->save();
            }

            // 3) Update deed_id
            if (isset($data['deed_id']) && (int)$data['deed_id'] !== (int)$activity->deed_id) {
                $activity->deed_id = $data['deed_id'];
                $activity->save();
            }

            $toAdd = [];
            $actuallyRemoved = [];

            // 4) Jika klien berubah → reset pivot & regen doc-req + sinkronisasi client_drafts
            if (isset($data['client_ids'])) {
                // Ambil existing sebelum dihapus (untuk diff)
                $existingIdsBefore = ClientActivity::where('activity_id', $activity->id)
                    ->orderBy('order', 'asc')
                    ->pluck('user_id')
                    ->all();

                // Reset pivot client_activity
                ClientActivity::where('activity_id', $activity->id)->delete();

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
                if ($rows) ClientActivity::insert($rows);

                // Regen document requirements untuk kombinasi activity × klien
                DocumentRequirement::where('activity_notaris_id', $activity->id)->delete();

                $actReq = Requirement::where('activity_id', $activity->id)->get();
                if ($actReq->count() && !empty($orderedClientIds)) {
                    $docRows = [];
                    foreach ($actReq as $req) {
                        foreach ($orderedClientIds as $uid) {
                            $docRows[] = [
                                'activity_notaris_id' => $activity->id,
                                'user_id'             => $uid,
                                'requirement_id'      => $req->id,
                                'requirement_name'    => $req->name,
                                'is_file_snapshot'    => (bool)$req->is_file,
                                'value'               => null,
                                'file'                => null,
                                'file_path'           => null,
                                'status_approval'     => 'pending',
                                'created_at'          => $now,
                                'updated_at'          => $now,
                            ];
                        }
                    }
                    if ($docRows) DocumentRequirement::insert($docRows);
                }

                // ===== Sinkronisasi CLIENT DRAFTS =====
                $draft = DraftDeed::firstOrCreate(
                    ['activity_id' => $activity->id],
                    [
                        'custom_value_template' => null,
                        'reading_schedule'      => null,
                        'status_approval'       => 'pending',
                        'file'                  => null,
                        'file_path'             => null,
                        'created_at'            => $now,
                        'updated_at'            => $now,
                    ]
                );

                $locked = ClientDraft::where('draft_deed_id', $draft->id)
                    ->whereIn('status_approval', ['approved', 'rejected'])
                    ->exists();

                if ($locked) {
                    throw new \Illuminate\Validation\ValidationException(
                        Validator::make([], []),
                        response()->json([
                            'success' => false,
                            'message' => 'Tidak dapat mengubah daftar klien karena sudah ada yang menyetujui/menolak draft.',
                            'data'    => null
                        ], 422)
                    );
                }

                // Diff untuk email: toAdd / toRemove
                $existingIds = ClientDraft::where('draft_deed_id', $draft->id)
                    ->pluck('user_id')
                    ->all();

                $toAdd    = array_values(array_diff($orderedClientIds, $existingIds));
                $toRemove = array_values(array_diff($existingIds, $orderedClientIds));

                // Tambah ClientDraft baru
                if (!empty($toAdd)) {
                    $cdRows = [];
                    foreach ($toAdd as $uid) {
                        $cdRows[] = [
                            'user_id'         => $uid,
                            'draft_deed_id'   => $draft->id,
                            'status_approval' => 'pending',
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }
                    ClientDraft::insert($cdRows);
                }

                // Hapus hanya yang masih pending
                if (!empty($toRemove)) {
                    $pendingToRemove = ClientDraft::where('draft_deed_id', $draft->id)
                        ->whereIn('user_id', $toRemove)
                        ->where('status_approval', 'pending')
                        ->pluck('user_id')
                        ->all();

                    if (!empty($pendingToRemove)) {
                        ClientDraft::where('draft_deed_id', $draft->id)
                            ->whereIn('user_id', $pendingToRemove)
                            ->delete();

                        // yang benar-benar dihapus → email "removed"
                        $actuallyRemoved = $pendingToRemove;
                    }
                }
                // ===== END sinkronisasi client drafts =====
            }

            // Eager load untuk response
            $activity->load([
                'deed',
                'notaris',
                'track',
                'clients' => fn($q) => $q->orderBy('client_activity.order', 'asc'),
                'clientActivities' => fn($q) => $q->orderBy('order', 'asc'),
                'requirements',
                'documentRequirements',
                'draft.clientDrafts.user',
                'schedules',
            ]);

            return [$activity, $toAdd, $actuallyRemoved];
        });

        // ===== Kirim email setelah commit =====
        $addUsers = !empty($toAdd) ? User::whereIn('id', $toAdd)->get() : collect();
        $removeUsers = !empty($actuallyRemoved) ? User::whereIn('id', $actuallyRemoved)->get() : collect();

        DB::afterCommit(function () use ($addUsers, $removeUsers, $activity) {
            foreach ($addUsers as $u) {
                try {
                    $this->notifyClientActivity($u, $activity, 'added');
                } catch (\Throwable $e) {
                }
            }
            foreach ($removeUsers as $u) {
                try {
                    $this->notifyClientActivity($u, $activity, 'removed');
                } catch (\Throwable $e) {
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil diperbarui',
            'data'    => $activity
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $isAdmin = (int)$user->role_id === 1 || $user->tokenCan('admin'); // fleksibel

        // Admin: boleh akses activity mana pun; Notaris: hanya miliknya
        $activityQuery = Activity::where('id', $id);
        if (!$isAdmin) {
            $activityQuery->where('user_notaris_id', $user->id);
        }
        $activity = $activityQuery->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // Blokir hapus jika sudah approved (AMAN)
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

        DB::beginTransaction();
        try {
            $trackId = $activity->track_id;

            // Hapus anak-anak
            $activity->documentRequirements()->delete();
            $activity->requirements()->delete();
            $activity->draft()->delete();       // pastikan FK cascade untuk client_drafts, kalau tidak: hapus manual
            $activity->schedules()->delete();
            $activity->clientActivities()->delete();

            // Hapus activity
            $activity->delete();

            // Hapus track (opsional)
            if ($trackId) {
                Track::where('id', $trackId)->delete();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil dihapus',
                'data'    => null
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat menghapus aktivitas.',
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

        $activity->load('track');

        return response()->json([
            'success' => true,
            'message' => 'Status Doc aktivitas berhasil ditandai selesai cui',
            'data'    => $activity->track
        ], 200);
    }
}
