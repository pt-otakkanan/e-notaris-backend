<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Activity;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'activity_id' => 'required|integer|exists:activity,id',
            'date'        => 'required|date_format:Y-m-d',
            'time'        => 'required|date_format:H:i',
            'location'    => 'nullable|string|max:500',
            'notes'       => 'nullable|string|max:500',
        ], [
            'location.string'     => 'Lokasi harus berupa teks.',
            'location.max'        => 'Lokasi maksimal 500 karakter.',
            'activity_id.required' => 'Activity wajib diisi.',
            'activity_id.integer' => 'Activity tidak valid.',
            'activity_id.exists'  => 'Activity tidak ditemukan.',
            'date.required'       => 'Tanggal wajib diisi.',
            'date.date_format'    => 'Format tanggal harus Y-m-d.',
            'time.required'       => 'Waktu wajib diisi.',
            'time.date_format'    => 'Format waktu harus H:i (24 jam).',
            'notes.string'        => 'Catatan harus berupa teks.',
            'notes.max'           => 'Catatan maksimal 500 karakter.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $payload = $validasi->validated();

        $schedule = DB::transaction(function () use ($payload) {
            // buat jadwal
            $schedule = Schedule::create($payload);

            // set track.status_schedule = 'done'
            $this->markScheduleStepDone($payload['activity_id']);

            return $schedule;
        });

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat',
            'data'    => $schedule,
        ], 201);
    }

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
            'location'    => 'nullable|string|max:500',
            'notes'       => 'nullable|string|max:500',
        ], [
            'location.string'     => 'Lokasi harus berupa teks.',
            'location.max'        => 'Lokasi maksimal 500 karakter.',
            'activity_id.required' => 'Activity wajib diisi.',
            'activity_id.integer' => 'Activity tidak valid.',
            'activity_id.exists'  => 'Activity tidak ditemukan.',
            'date.required'       => 'Tanggal wajib diisi.',
            'date.date_format'    => 'Format tanggal harus Y-m-d.',
            'time.required'       => 'Waktu wajib diisi.',
            'time.date_format'    => 'Format waktu harus H:i (24 jam).',
            'notes.string'        => 'Catatan harus berupa teks.',
            'notes.max'           => 'Catatan maksimal 500 karakter.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        DB::transaction(function () use ($schedule, $data) {
            foreach (['activity_id', 'date', 'time', 'notes', 'location'] as $f) {
                if (array_key_exists($f, $data)) {
                    $schedule->{$f} = $data[$f];
                }
            }
            $schedule->save();

            // pastikan track step schedule = done (kalau sudah done, tetap dipertahankan)
            $this->markScheduleStepDone($schedule->activity_id);
        });

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data'    => $schedule
        ], 200);
    }

    /**
     * Set track.status_schedule menjadi 'done' untuk activity terkait.
     * Aman terhadap kondisi tidak ditemukan (no-op).
     */
    private function markScheduleStepDone(int $activityId): void
    {
        // Ambil activity beserta track-nya
        $activity = Activity::select(['id', 'track_id'])->with('track')->find($activityId);
        if (!$activity) return;

        // Jika relasi track tidak otomatis, fallback cari via track_id
        $track = $activity->track ?? Track::find($activity->track_id);
        if (!$track) return;

        if ($track->status_schedule !== 'done') {
            $track->status_schedule = 'done';
            $track->status_sign = 'todo';
            $track->save();
        }
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

    /**
     * GET /schedule/user
     * Ambil semua jadwal milik user login (sebagai penghadap/klien).
     */
    public function allScheduleUser(Request $request)
    {
        $user    = $request->user();
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $scope    = $request->query('scope'); // today|upcoming|past
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');
        $q        = $request->query('search');

        // ambil semua schedule yang activity-nya punya relasi ke user ini lewat clientActivities
        // tambahkan jika itu notaris maka dilihat dari activity.user_notaris_id = user.id
        $query = Schedule::with('activity')
            ->whereHas('activity', function ($sub) use ($user) {
                $sub->whereHas('clientActivities', function ($sub2) use ($user) {
                    $sub2->where('user_id', $user->id);
                });
                // jika user adalah notaris, maka ambil juga jadwal dari activity yang dia buat
                if ($user->role_id === 3) {
                    $sub->orWhere('user_notaris_id', $user->id);
                }
            });
        // scope (today|upcoming|past)
        if ($scope === 'today') {
            $query->today();
        } elseif ($scope === 'upcoming') {
            $query->upcoming();
        } elseif ($scope === 'past') {
            $query->past();
        }

        // filter by date range
        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        // search di notes / location
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('notes', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }

        $schedules = $query
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar jadwal user berhasil diambil',
            'data'    => $schedules->items(),
            'meta'    => [
                'current_page' => $schedules->currentPage(),
                'per_page'     => $schedules->perPage(),
                'total'        => $schedules->total(),
                'last_page'    => $schedules->lastPage(),
            ]
        ], 200);
    }
}
