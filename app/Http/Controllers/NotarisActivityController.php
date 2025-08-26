<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentRequirement;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class NotarisActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');
        $approvalStatus = $request->query('status'); // Ganti dari 'status' ke 'approval_status' untuk clarity
        $perPage = (int)($request->query('per_page', 10));
        $perPage = $perPage > 0 ? $perPage : 10;

        $query = Activity::with(['deed', 'notaris', 'firstClient', 'secondClient', 'schedules'])
            ->where('user_notaris_id', $user->id);

        // Search by tracking code, activity name, or deed name
        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('tracking_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('deed', function ($deedQuery) use ($search) {
                        $deedQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by approval status
        if ($approvalStatus && in_array($approvalStatus, ['pending', 'approved', 'rejected'])) {
            $query->where(function ($statusQuery) use ($approvalStatus) {

                if ($approvalStatus === 'approved') {
                    // Untuk status approved: semua penghadap yang diperlukan harus approved
                    $statusQuery->where(function ($approvedQuery) {
                        $approvedQuery->whereHas('deed', function ($deedQuery) {
                            // Jika double client, kedua penghadap harus approved
                            $deedQuery->where('is_double_client', true);
                        })
                            ->where('first_client_approval', 'approved')
                            ->where('second_client_approval', 'approved')
                            ->orWhere(function ($singleQuery) {
                                // Jika single client, hanya first client yang perlu approved
                                $singleQuery->whereHas('deed', function ($deedQuery) {
                                    $deedQuery->where('is_double_client', false);
                                })
                                    ->where('first_client_approval', 'approved');
                            });
                    });
                } elseif ($approvalStatus === 'rejected') {
                    // Untuk status rejected: setidaknya satu penghadap rejected
                    $statusQuery->where('first_client_approval', 'rejected')
                        ->orWhere('second_client_approval', 'rejected');
                } elseif ($approvalStatus === 'pending') {
                    // Untuk status pending: setidaknya satu penghadap masih pending
                    // DAN tidak ada yang rejected (karena jika ada rejected, statusnya bukan pending lagi)
                    $statusQuery->where(function ($pendingQuery) {
                        $pendingQuery->where(function ($condition1) {
                            // Double client: belum semua approved DAN tidak ada yang rejected
                            $condition1->whereHas('deed', function ($deedQuery) {
                                $deedQuery->where('is_double_client', true);
                            })
                                ->where(function ($doubleCondition) {
                                    $doubleCondition->where('first_client_approval', 'pending')
                                        ->orWhere('second_client_approval', 'pending');
                                })
                                ->where('first_client_approval', '!=', 'rejected')
                                ->where('second_client_approval', '!=', 'rejected');
                        })
                            ->orWhere(function ($condition2) {
                                // Single client: masih pending
                                $condition2->whereHas('deed', function ($deedQuery) {
                                    $deedQuery->where('is_double_client', false);
                                })
                                    ->where('first_client_approval', 'pending');
                            });
                    });
                }
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar aktivitas berhasil diambil',
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'last_page' => $activities->lastPage(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem(),
            ]
        ], 200);
    }

    /**
     * GET /activities/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $activity = Activity::with([
            'deed',
            'notaris',
            'firstClient.identity',
            'secondClient.identity',
            'schedules'
        ])->where('id', $id)
            ->where('user_notaris_id', $user->id) // Pastikan hanya mengambil aktivitas milik notaris ini
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

    public function store(Request $request)
    {
        $user = $request->user();
        $validasi = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'deed_id'          => 'required|exists:deeds,id',
            'first_client_id'  => 'required|exists:users,id,role_id,2',
            'second_client_id' => 'nullable|exists:users,id,role_id,2',
        ], [
            'name.required'             => 'Nama akta wajib diisi.',
            'name.string'               => 'Nama akta harus berupa teks.',
            'name.max'                  => 'Nama akta maksimal 255 karakter.',
            'deed_id.required'          => 'ID akta wajib diisi.',
            'deed_id.exists'            => 'Akta yang dipilih tidak valid.',
            'first_client_id.required'  => 'ID klien pertama wajib diisi.',
            'first_client_id.exists'    => 'Klien pertama harus memiliki role penghadap.',
            'second_client_id.exists'   => 'Klien kedua harus memiliki role penghadap.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        // Cek is_double_client
        $deed = Deed::with('requirements')->find($data['deed_id']);
        if ($deed->is_double_client && !$request->has('second_client_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Akta ini memerlukan klien kedua',
                'data'    => null,
            ], 422);
        }

        return DB::transaction(function () use ($user, $deed, $data) {
            // Buat Activity
            $payload = [
                'name'                    => $data['name'],
                'deed_id'                 => $deed->id,
                'user_notaris_id'         => $user->id,
                'activity_notaris_id'     => $user->id,
                'first_client_id'         => $data['first_client_id'],
                'second_client_id'        => $data['second_client_id'] ?? null,
                'tracking_code'           => 'ACT-' . strtoupper(Str::random(8)),
                'status_approval'         => 'pending',
                'first_client_approval'   => 'pending',
                // 'second_client_approval'  => $deed->is_double_client ? 'pending' : null,
                'second_client_approval'  => 'pending',
            ];

            $activity = Activity::create($payload);

            // Tentukan target user
            $targets = [$activity->first_client_id];
            if ($deed->is_double_client && $activity->second_client_id) {
                $targets[] = $activity->second_client_id;
            }

            // Buat DocumentRequirement kosong sesuai Requirement
            $rows = [];
            $now  = now();

            foreach ($deed->requirements as $req) {
                foreach ($targets as $uid) {
                    $rows[] = [
                        'activity_notaris_id' => $activity->id,
                        'user_id'             => $uid,
                        'requirement_id'      => $req->id,
                        'requirement_name'    => $req->name,
                        'is_file_snapshot'    => (bool)$req->is_file,
                        'value'               => null,
                        'file'                => null,
                        'file_path'           => null,
                        'status_approval'     => 'pending'
                    ];
                }
            }

            if (!empty($rows)) {
                DocumentRequirement::insert($rows);
            }

            $activity->load([
                'deed.requirements',
                'notaris',
                'firstClient',
                'secondClient',
                'documentRequirements.requirement'
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
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $activity = Activity::where('id', $id)
            ->where('user_notaris_id', $user->id) // Pastikan hanya mengupdate aktivitas milik notaris ini
            ->first();
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'             => 'sometimes|required|string|max:255',
            'deed_id'          => 'sometimes|required|exists:deeds,id',
            'user_notaris_id'  => 'sometimes|required|exists:users,id',
            'first_client_id'  => 'sometimes|required|exists:users,id,role_id,2',
            'second_client_id' => 'nullable|exists:users,id,role_id,2',
            'status_approval'  => 'sometimes|required|in:pending,approved,rejected',
            'first_client_approval'  => 'sometimes|required|in:pending,approved,rejected',
            'second_client_approval' => 'nullable|in:pending,approved,rejected',
        ], [
            'name.required'             => 'Nama akta wajib diisi.',
            'name.string'               => 'Nama akta harus berupa teks.',
            'name.max'                  => 'Nama akta maksimal 255 karakter.',
            'deed_id.required'          => 'ID akta wajib diisi.',
            'deed_id.exists'            => 'Akta yang dipilih tidak valid.',
            'user_notaris_id.required'  => 'ID notaris wajib diisi.',
            'user_notaris_id.exists'    => 'Notaris yang dipilih tidak valid.',
            'first_client_id.required'  => 'ID klien pertama wajib diisi.',
            'first_client_id.exists'    => 'Klien pertama harus memiliki role client.',
            'second_client_id.exists'   => 'Klien kedua harus memiliki role client.',
            'status_approval.required'  => 'Status approval wajib diisi.',
            'status_approval.in'        => 'Status approval harus pending, approved, atau rejected.',
            'first_client_approval.required'  => 'Approval klien pertama wajib diisi.',
            'first_client_approval.in'        => 'Approval klien pertama harus pending, approved, atau rejected.',
            'second_client_approval.in'       => 'Approval klien kedua harus pending, approved, atau rejected.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        // Jika deed_id diubah, cek apakah memerlukan double client
        if (isset($data['deed_id'])) {
            $deed = Deed::find($data['deed_id']);
            if ($deed->is_double_client && !($data['second_client_id'] ?? $activity->second_client_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akta ini memerlukan klien kedua',
                    'data'    => null,
                ], 422);
            }
        }

        // Update activity_notaris_id jika user_notaris_id diubah
        if (isset($data['user_notaris_id'])) {
            $data['activity_notaris_id'] = $data['user_notaris_id'];
        }

        foreach ($data as $key => $value) {
            $activity->{$key} = $value;
        }

        $activity->first_client_approval = 'pending'; // Reset approval klien pertama
        if ($activity->second_client_id) {
            $activity->second_client_approval = 'pending'; // Reset approval klien kedua jika ada
        } else {
            $activity->second_client_approval = null; // Pastikan null jika tidak ada klien kedua
        }

        $activity->save();
        $activity->load(['deed', 'notaris', 'firstClient', 'secondClient']);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil diperbarui',
            'data'    => $activity
        ], 200);
    }

    /**
     * DELETE /activities/{id}
     * Cegah hapus jika masih ada relasi penting
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::where('id', $id)
            ->where('user_notaris_id', $user->id) // hanya milik notaris ini
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // Blokir kalau ada yang sudah approved
        if (
            $activity->status_approval === 'approved'
            || $activity->first_client_approval === 'approved'
            || $activity->second_client_approval === 'approved'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas yang sudah disetujui tidak dapat dihapus.',
                'data'    => null
            ], 409);
        }

        try {
            DB::beginTransaction();

            // 1) Hapus DocumentRequirement (hapus file Cloudinary jika ada)
            $activity->documentRequirements()
                ->select(['id', 'file_path']) // ambil yang perlu saja
                ->chunkById(200, function ($docs) {
                    foreach ($docs as $doc) {
                        if (!empty($doc->file_path)) {
                            try {
                                Cloudinary::destroy($doc->file_path);
                            } catch (\Throwable $e) {
                                // lanjut hapus DB meski file gagal dihapus
                            }
                        }
                        // Hapus baris doc satu per satu (aman untuk event/observer)
                        DocumentRequirement::where('id', $doc->id)->delete();
                    }
                });

            // 2) Hapus DraftDeed (kalau punya file, tambahkan logic destroy seperti di atas)
            $activity->draftDeeds()->delete();

            // 3) Hapus Schedules
            $activity->schedules()->delete();

            // 4) Terakhir, hapus Activity
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
     * Update status approval ke approved
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
     * Update status approval ke rejected
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

    public function getUsers(Request $request)
    {
        $search  = $request->query('search');

        $query = User::query()
            ->select(['id', 'name', 'email', 'telepon', 'gender', 'status_verification', 'role_id'])
            ->with(['identity:id,user_id,file_photo'])
            ->where('role_id', 2) // hanya penghadap
            ->where('status_verification', 'approved');

        if ($search) {
            $query->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        // kirim langsung sebagai opsi select {value,label}
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
