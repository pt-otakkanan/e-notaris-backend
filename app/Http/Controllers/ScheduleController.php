<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $perPage   = (int)($request->query('per_page', 10));
        $perPage   = $perPage > 0 ? $perPage : 10;
        $activityId = $request->query('activity_id');
        $scope     = $request->query('scope'); // today|upcoming|past
        $dateFrom  = $request->query('date_from');
        $dateTo    = $request->query('date_to');
        $q         = $request->query('search');

        $query = Schedule::with('activity')
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id);
            });

        // scope (today|upcoming|past)
        if ($scope === 'today') {
            $query->today();
        } elseif ($scope === 'upcoming') {
            $query->upcoming();
        } elseif ($scope === 'past') {
            $query->past();
        }

        // filter activity
        if ($activityId) {
            $query->where('activity_id', $activityId);
        }

        // filter by date range
        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        // search in notes
        if ($q) {
            $query->where('notes', 'like', "%{$q}%");
        }

        // urutkan terdekat terlebih dahulu
        $schedules = $query
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar jadwal berhasil diambil',
            'data'    => $schedules->items(),
            'meta'    => [
                'current_page' => $schedules->currentPage(),
                'per_page'     => $schedules->perPage(),
                'total'        => $schedules->total(),
                'last_page'    => $schedules->lastPage(),
            ]
        ], 200);
    }

    /**
     * GET /schedules/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $schedule = Schedule::with('activity')
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->where('user_notaris_id', $user->id);
            })->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail jadwal berhasil diambil',
            'data'    => $schedule
        ], 200);
    }

    /**
     * POST /schedules
     * Body: activity_id, date (Y-m-d), time (H:i), notes (optional)
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'activity_id' => 'required|integer|exists:activity,id',
            'date'        => 'required|date_format:Y-m-d',
            'time'        => 'required|date_format:H:i',
            'location'       => 'nullable|string|max:500',
            'notes'       => 'nullable|string|max:500',
        ], [
            'location.string'         => 'Lokasi harus berupa teks.',
            'location.max'            => 'Lokasi maksimal 500 karakter.',
            'activity_id.required' => 'Activity wajib diisi.',
            'activity_id.integer'  => 'Activity tidak valid.',
            'activity_id.exists'   => 'Activity tidak ditemukan.',
            'date.required'        => 'Tanggal wajib diisi.',
            'date.date_format'     => 'Format tanggal harus Y-m-d.',
            'time.required'        => 'Waktu wajib diisi.',
            'time.date_format'     => 'Format waktu harus H:i (24 jam).',
            'notes.string'         => 'Catatan harus berupa teks.',
            'notes.max'            => 'Catatan maksimal 500 karakter.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $schedule = Schedule::create($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat',
            'data'    => $schedule,
        ], 201);
    }

    /**
     * PUT /schedules/{id}
     * Body: activity_id, date (Y-m-d), time (H:i), notes (optional)
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'activity_id' => 'required|integer|exists:activity,id',
            'date'        => 'required|date_format:Y-m-d',
            'time'        => 'required|date_format:H:i',
            'location'       => 'nullable|string|max:500',
            'notes'       => 'nullable|string|max:500',
        ], [
            'location.string'         => 'Lokasi harus berupa teks.',
            'location.max'            => 'Lokasi maksimal 500 karakter.',
            'activity_id.required' => 'Activity wajib diisi.',
            'activity_id.integer'  => 'Activity tidak valid.',
            'activity_id.exists'   => 'Activity tidak ditemukan.',
            'date.required'        => 'Tanggal wajib diisi.',
            'date.date_format'     => 'Format tanggal harus Y-m-d.',
            'time.required'        => 'Waktu wajib diisi.',
            'time.date_format'     => 'Format waktu harus H:i (24 jam).',
            'notes.string'         => 'Catatan harus berupa teks.',
            'notes.max'            => 'Catatan maksimal 500 karakter.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        foreach (['activity_id', 'date', 'time', 'notes', 'location'] as $f) {
            if (array_key_exists($f, $data)) {
                $schedule->{$f} = $data[$f];
            }
        }

        $schedule->save();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data'    => $schedule
        ], 200);
    }

    /**
     * DELETE /schedules/{id}
     */
    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus',
            'data'    => null
        ], 200);
    }
}
