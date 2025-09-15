<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ClientActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientActivityController extends Controller
{
    /**
     * GET /activity (untuk klien/penghadap)
     * Query: search, status(pending|approved|rejected), per_page
     * Menampilkan activity yang melibatkan user login via pivot client_activity
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $search  = $request->query('search');
        $status  = $request->query('status'); // status pivot milik user
        $perPage = (int)($request->query('per_page', 10)) ?: 10;

        $query = Activity::with([
            'deed',
            'notaris',
            'track',
            // urutkan clients sesuai pivot.order (fallback id)
            'clients' => function ($q) {
                $q->select('users.id', 'users.name', 'users.email')
                    ->orderBy('client_activity.order', 'asc')
                    ->orderBy('client_activity.id', 'asc');
            },
            // urutkan records pivot juga
            'clientActivities' => function ($q) {
                $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            },
            'schedules'
        ])
            ->whereHas('clients', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('tracking_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('deed', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter berdasarkan status pivot milik user login
        if ($status && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->whereHas('clientActivities', function ($pq) use ($user, $status) {
                $pq->where('user_id', $user->id)
                    ->where('status_approval', $status);
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
     * GET /activity/{id} (untuk klien)
     * Hanya bisa melihat activity yang melibatkan dirinya lewat pivot
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::with([
            'deed',
            'notaris',
            'track',
            'documentRequirements' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            },
            // urutkan clients sesuai pivot.order → gunakan nama tabel pivot: client_activity
            'clients' => function ($q) {
                $q->with('identity')
                    ->orderBy('client_activity.order', 'asc')
                    ->orderBy('client_activity.id', 'asc');
            },
            'clientActivities' => function ($q) {
                $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            },
            'schedules',
        ])
            ->where('id', $id)
            ->whereHas('clients', function ($q) use ($user) {
                $q->where('users.id', $user->id);
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
     * POST /activity/approval/{id}
     * Body: { "approval_status": "approved" | "rejected" }
     * Mengubah status_approval di pivot client_activity untuk user login,
     * lalu update status respond di track berdasarkan agregat seluruh client.
     */
    public function clientApproval(Request $request, $id)
    {
        $user = $request->user();

        $activity = Activity::with(['deed', 'clientActivities', 'track'])->find($id);
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan', 'data' => null], 404);
        }

        $pivot = ClientActivity::where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Anda bukan klien pada aktivitas ini', 'data' => null], 403);
        }

        $validasi = Validator::make($request->all(), [
            'approval_status' => 'required|in:approved,rejected',
        ]);
        if ($validasi->fails()) {
            return response()->json(['success' => false, 'message' => 'Proses validasi gagal', 'data' => $validasi->errors()], 422);
        }

        $status = $request->input('approval_status');

        return DB::transaction(function () use ($pivot, $status, $activity) {
            // Idempotent: kalau status sama, tidak perlu update
            if ($pivot->status_approval !== $status) {
                $pivot->status_approval = $status;
                $pivot->save();
            }

            // Hitung agregat terbaru
            $activity->loadMissing('clientActivities');

            $anyRejected = $activity->clientActivities()
                ->where('status_approval', 'rejected')
                ->exists();

            $allApproved = !$activity->clientActivities()
                ->where('status_approval', '!=', 'approved')
                ->exists();

            $track = $activity->track; // sudah diload
            if ($track) {
                if ($anyRejected) {
                    $track->status_respond = 'rejected';
                } elseif ($allApproved) {
                    $track->status_respond = 'done';
                    $track->status_docs = 'todo';
                    $track->status_draft = 'todo';
                }
                $track->save();
            }

            $activity->load(['deed', 'clients', 'clientActivities', 'track']);

            return response()->json([
                'success' => true,
                'message' => 'Status approval berhasil diperbarui',
                'data'    => [
                    'pivot'    => $pivot,
                    'activity' => $activity,
                ],
            ], 200);
        });
    }
}
