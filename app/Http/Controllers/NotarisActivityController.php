<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Deed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotarisActivityController extends Controller
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

        $query = Activity::with(['deed', 'notaris', 'firstClient', 'secondClient']);
        $query->where('user_notaris_id', $user->id);
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
            $query->where('status_approval', $status);
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
            'documentRequirements',
            'draftDeeds',
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

    /**
     * POST /activities
     * Body: deed_id, user_notaris_id, first_client_id, second_client_id (optional)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $validasi = Validator::make($request->all(), [
            'deed_id'          => 'required|exists:deeds,id',
            'first_client_id'  => 'required|exists:users,id,role_id,2',
            'second_client_id' => 'nullable|exists:users,id,role_id,2',
        ], [
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

        // Cek apakah deed memerlukan double client
        $deed = Deed::find($data['deed_id']);
        if ($deed->is_double_client && !$request->has('second_client_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Akta ini memerlukan klien kedua',
                'data'    => null,
            ], 422);
        }

        // Generate tracking code
        $data['tracking_code'] = 'ACT-' . strtoupper(Str::random(8));
        $data['activity_notaris_id'] = $user->id;
        $data['status_approval'] = 'pending';
        $data['user_notaris_id'] = $user->id;
        $data['first_client_approval'] = 'pending';
        $data['second_client_approval'] = 'pending';

        $activity = Activity::create($data);
        $activity->load(['deed', 'notaris', 'firstClient', 'secondClient']);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil dibuat',
            'data'    => $activity,
        ], 201);
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
            'deed_id'          => 'sometimes|required|exists:deeds,id',
            'user_notaris_id'  => 'sometimes|required|exists:users,id',
            'first_client_id'  => 'sometimes|required|exists:users,id,role_id,2',
            'second_client_id' => 'nullable|exists:users,id,role_id,2',
            'status_approval'  => 'sometimes|required|in:pending,approved,rejected',
            'first_client_approval'  => 'sometimes|required|in:pending,approved,rejected',
            'second_client_approval' => 'nullable|in:pending,approved,rejected',
        ], [
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
            ->where('user_notaris_id', $user->id) // Pastikan hanya menghapus aktivitas milik notaris ini
            ->first();
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // Cek relasi sebelum hapus
        if (
            $activity->documentRequirements()->exists() ||
            $activity->draftDeeds()->exists() ||
            $activity->schedules()->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak dapat dihapus karena masih memiliki data terkait (documents/drafts/schedules).',
                'data'    => null
            ], 409); // Conflict
        }

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil dihapus',
            'data'    => null
        ], 200);
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

    /**
     * PUT /activities/{id}/client-approve
     * Update client approval status
     * Body: client_type (first|second), approval_status (approved|rejected)
     */
    public function clientApprove(Request $request, $id)
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'client_type' => 'required|in:first,second',
            'approval_status' => 'required|in:approved,rejected',
        ], [
            'client_type.required' => 'Tipe klien wajib diisi.',
            'client_type.in' => 'Tipe klien harus first atau second.',
            'approval_status.required' => 'Status approval wajib diisi.',
            'approval_status.in' => 'Status approval harus approved atau rejected.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $clientType = $request->client_type;
        $approvalStatus = $request->approval_status;

        if ($clientType === 'first') {
            $activity->first_client_approval = $approvalStatus;
        } else {
            if (!$activity->second_client_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktivitas ini tidak memiliki klien kedua',
                    'data'    => null
                ], 422);
            }
            $activity->second_client_approval = $approvalStatus;
        }

        $activity->save();

        return response()->json([
            'success' => true,
            'message' => 'Status approval klien berhasil diperbarui',
            'data'    => $activity
        ], 200);
    }
}
