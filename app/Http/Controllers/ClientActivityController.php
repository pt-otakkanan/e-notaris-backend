<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientActivityController extends Controller
{
    /**
     * GET /activities
     * Query opsional: search, status, page, per_page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $search   = $request->query('search');
        $status   = $request->query('status');
        $perPage  = (int)($request->query('per_page', 10));
        $perPage  = $perPage > 0 ? $perPage : 10;

        $query = Activity::with(['deed', 'notaris', 'firstClient', 'secondClient', 'schedules']);
        $query->where('first_client_id', $user->id)->orWhere('second_client_id', $user->id);
        // Search by tracking code or deed name
        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('tracking_code', 'like', "%{$search}%")
                    ->orWhereHas('deed', function ($deedQuery) use ($search) {
                        $deedQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('first_client_approval', $status)->orWhere('second_client_approval', $status);
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
            'firstClient',
            'secondClient',
            'schedules'
        ])->where('id', $id)
            ->where('first_client_id', $user->id)
            ->orWhere('second_client_id', $user->id)
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

    public function clientApproval(Request $request, $id)
    {
        // Ambil activity + relasi deed (buat cek is_double_client)
        $activity = Activity::with('deed')->find($id);
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        // Validasi: sekarang cuma butuh approval_status
        $validasi = Validator::make($request->all(), [
            'approval_status' => 'required|in:approved,rejected',
        ], [
            'approval_status.required' => 'Status approval wajib diisi.',
            'approval_status.in'       => 'Status approval harus approved atau rejected.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        // Tentukan user saat ini adalah first atau second client
        $userId = (int) optional($request->user())->id;
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak terautentikasi.',
                'data'    => null,
            ], 401);
        }

        $clientType = null;
        if ((int) $activity->first_client_id === $userId) {
            $clientType = 'first';
        } elseif ($activity->second_client_id && (int) $activity->second_client_id === $userId) {
            $clientType = 'second';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar sebagai penghadap pada aktivitas ini.',
                'data'    => null,
            ], 403);
        }

        $approvalStatus = $request->approval_status;

        // Set status approval sesuai clientType yang terdeteksi
        if ($clientType === 'first') {
            $activity->first_client_approval = $approvalStatus;
        } else {
            // Safety: kalau tidak ada second_client_id tapi user mengaku second
            if (!$activity->second_client_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktivitas ini tidak memiliki klien kedua.',
                    'data'    => null,
                ], 422);
            }
            $activity->second_client_approval = $approvalStatus;
        }

        // Update status_approval global berdasarkan kebutuhan jumlah klien
        $isDouble = (bool) optional($activity->deed)->is_double_client;

        if ($approvalStatus === 'rejected') {
            // Jika salah satu menolak → langsung rejected
            $activity->status_approval = 'rejected';
        } else {
            // approved dari client yang submit
            if ($isDouble) {
                // butuh dua-duanya approved untuk set approved
                if (
                    $activity->first_client_approval === 'approved' &&
                    $activity->second_client_approval === 'approved'
                ) {
                    $activity->status_approval = 'approved';
                } else {
                    $activity->status_approval = 'pending';
                }
            } else {
                // single client: cukup first approved
                $activity->status_approval =
                    $activity->first_client_approval === 'approved'
                    ? 'approved'
                    : 'pending';
            }
        }

        $activity->save();

        // Kembalikan activity terbaru (opsional muat relasi)
        $activity->loadMissing(['deed', 'firstClient', 'secondClient']);

        return response()->json([
            'success' => true,
            'message' => 'Status approval klien berhasil diperbarui',
            'data'    => [
                'client_type_detected' => $clientType,
                'activity'             => $activity,
            ],
        ], 200);
    }
}
