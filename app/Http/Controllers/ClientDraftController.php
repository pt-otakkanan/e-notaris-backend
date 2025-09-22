<?php

namespace App\Http\Controllers;

use App\Models\DraftDeed;
use App\Models\ClientDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientDraftController extends Controller
{
    /**
     * GET /drafts (untuk klien/penghadap)
     * Query: search, status(pending|approved|rejected), per_page
     * Menampilkan draft yang melibatkan user login via pivot client_drafts
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $search  = $request->query('search');
        $status  = $request->query('status'); // status pivot milik user
        $perPage = (int)($request->query('per_page', 10)) ?: 10;

        $query = DraftDeed::with([
            // konteks activity
            'activity.deed',
            'activity.notaris',
            'activity.track',
            // seluruh clientDrafts urut, dan sertakan user-nya
            'clientDrafts' => function ($q) {
                $q->orderBy('id', 'asc')->with('user:id,name,email');
            },
        ])->whereHas('clientDrafts', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->whereHas('activity', function ($aq) use ($search) {
                    $aq->where('tracking_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhereHas('deed', function ($dq) use ($search) {
                            $dq->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        if ($status && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            // filter by status pivot untuk user login
            $query->whereHas('clientDrafts', function ($pq) use ($user, $status) {
                $pq->where('user_id', $user->id)
                    ->where('status_approval', $status);
            });
        }

        $drafts = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar draft berhasil diambil',
            'data'    => $drafts->items(),
            'meta'    => [
                'current_page' => $drafts->currentPage(),
                'per_page'     => $drafts->perPage(),
                'total'        => $drafts->total(),
                'last_page'    => $drafts->lastPage(),
                'from'         => $drafts->firstItem(),
                'to'           => $drafts->lastItem(),
            ]
        ], 200);
    }

    /**
     * GET /drafts/{id} (untuk klien)
     * Hanya bisa melihat draft yang melibatkan dirinya via pivot client_drafts
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $draft = DraftDeed::with([
            'activity.deed',
            'activity.notaris',
            'activity.track',
            // seluruh clientDrafts + user untuk konteks
            'clientDrafts' => function ($q) {
                $q->orderBy('id', 'asc')->with('user:id,name,email');
            },
        ])
            ->where('id', $id)
            ->whereHas('clientDrafts', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // optional: ikutkan pivot milik user saat ini sebagai convenience
        $myPivot = $draft->clientDrafts->firstWhere('user_id', $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Detail draft berhasil diambil',
            'data'    => [
                'draft'    => $draft,
                'my_pivot' => $myPivot,
            ],
        ], 200);
    }

    /**
     * POST /drafts/approval/{id}
     * Body: { "approval_status": "approved" | "rejected" }
     * Mengubah status_approval di pivot client_drafts untuk user login,
     * lalu update status_draft di track berdasarkan agregat seluruh klien.
     */
    public function clientApproval(Request $request, $id)
    {
        $user = $request->user();

        $draft = DraftDeed::with(['activity.clientDrafts', 'activity.track'])->find($id);
        if (!$draft) {
            return response()->json(['success' => false, 'message' => 'Draft tidak ditemukan', 'data' => null], 404);
        }

        // pastikan user ini memang klien pada draft ini
        $pivot = ClientDraft::where('draft_deed_id', $draft->id) // ganti ke 'draft_id' jika kolommu itu
            ->where('user_id', $user->id)
            ->first();

        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Anda bukan klien pada draft ini', 'data' => null], 403);
        }

        $validasi = Validator::make($request->all(), [
            'approval_status' => 'required|in:approved,rejected',
        ]);
        if ($validasi->fails()) {
            return response()->json(['success' => false, 'message' => 'Proses validasi gagal', 'data' => $validasi->errors()], 422);
        }

        $status = $request->input('approval_status');

        return DB::transaction(function () use ($pivot, $status, $draft) {
            // Idempotent
            if ($pivot->status_approval !== $status) {
                $pivot->status_approval = $status;
                $pivot->save();
            }

            // Hitung agregat terbaru untuk semua klien pada draft ini
            $activity = $draft->activity()->with('clientDrafts')->first();

            $anyRejected = $activity->clientDrafts()
                ->where('client_drafts.status_approval', 'rejected') // ← kwalifikasi tabel
                ->exists();

            $allApproved = !$activity->clientDrafts()
                ->where('client_drafts.status_approval', '!=', 'approved') // ← kwalifikasi tabel
                ->exists();

            // Update track utk tahap draft
            $track = $activity->track;
            if ($track) {
                if ($anyRejected) {
                    $track->status_draft = 'rejected';
                } elseif ($allApproved) {
                    $track->status_docs    = 'done';
                    $track->status_draft    = 'done';
                    $track->status_schedule = 'todo'; // lanjut ke tahap berikutnya
                }
                //  else {
                //     // masih proses approval
                //     if ($track->status_draft !== 'pending') {
                //         $track->status_draft = 'pending';
                //     }
                // }
                $track->save();
            }

            // refresh data terkini
            $draft->load(['activity.deed', 'activity.notaris', 'activity.track', 'clientDrafts.user']);

            return response()->json([
                'success' => true,
                'message' => 'Status approval draft berhasil diperbarui',
                'data'    => [
                    'pivot' => $pivot,
                    'draft' => $draft,
                ],
            ], 200);
        });
    }
}
