<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Track;
use App\Models\Activity;
use App\Models\Schedule;
use App\Mail\ScheduleMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendScheduleNotificationJob;
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
            // ... pesan validasi kamu
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
            $schedule = Schedule::create($payload);
            $this->markScheduleStepDone($payload['activity_id']);
            return $schedule;
        });

        // === Kirim notifikasi setelah commit ===
        // $this->notifySchedule($schedule->fresh(['activity']), 'created');
        SendScheduleNotificationJob::dispatch($schedule->id, 'created');

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
            $this->markScheduleStepDone($schedule->activity_id);
        });

        // === Kirim notifikasi setelah commit ===
        // $this->notifySchedule($schedule->fresh(['activity']), 'updated');
        SendScheduleNotificationJob::dispatch($schedule->id, 'updated');

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data'    => $schedule
        ], 200);
    }

    public function destroy($id)
    {
        $schedule = Schedule::with('activity')->find($id);
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // simpan snapshot untuk email
        $snapshot = $schedule->replicate();
        $snapshot->setRelation('activity', $schedule->activity);

        DB::transaction(function () use ($schedule) {
            $schedule->delete();
        });

        // === Kirim notifikasi setelah commit ===
        // $this->notifySchedule($snapshot, 'deleted');
        SendScheduleNotificationJob::dispatch($schedule->id, 'deleted');

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus',
            'data'    => null
        ], 200);
    }

    // ======================= NOTIFIKASI SCHEDULE =======================

    /**
     * Kirim email notifikasi jadwal ke semua klien di activity & notaris.
     * @param Schedule $schedule (harus punya relasi 'activity' terload)
     * @param string   $type     'created'|'updated'|'deleted'
     */
    private function notifySchedule(Schedule $schedule, string $type): void
    {
        $activity = $schedule->activity ?? Activity::with('clientActivities.user')
            ->find($schedule->activity_id);

        if (!$activity) return;

        // Ambil notaris
        $notary = User::find($activity->user_notaris_id);

        // Ambil daftar klien dari relasi pivot (clientActivities -> user)
        // Pastikan Activity punya relasi:
        // public function clientActivities() { return $this->hasMany(ClientActivity::class); }
        // dan ClientActivity punya relasi user()
        $clients = $activity->clientActivities()
            ->with('user:id,name,email')
            ->get()
            ->pluck('user')
            ->filter(fn($u) => $u && $u->email);

        // detail umum
        $baseDetails = $this->buildScheduleMailDetails($activity, $schedule, $type);

        DB::afterCommit(function () use ($clients, $notary, $baseDetails) {
            try {
                // Kirim ke setiap klien
                foreach ($clients as $client) {
                    $details = $baseDetails;
                    $details['recipient_name'] = $client->name ?? 'Pengguna';

                    $mailable = new ScheduleMail($details, $details['subject'] ?? 'Notifikasi Jadwal');

                    $mailer = Mail::to($client->email, $client->name);
                    if ($notaryEmail = ($notary->email ?? null)) {
                        // BCC ke notaris (opsional)
                        $mailer->bcc($notaryEmail, $notary->name ?? 'Notaris');
                    }
                    $mailer->send($mailable);
                }

                // Kirim ke notaris juga (opsional)
                if ($notary && $notary->email) {
                    $details = $baseDetails;
                    $details['recipient_name'] = $notary->name ?? 'Notaris';
                    $mailable = new ScheduleMail($details, $details['subject'] ?? 'Notifikasi Jadwal');
                    Mail::to($notary->email, $notary->name)->send($mailable);
                }
            } catch (\Throwable $e) {
                // Diabaikan sesuai preferensi (tanpa \Log)
            }
        });
    }

    /**
     * Susun detail untuk email jadwal
     */
    private function buildScheduleMailDetails(Activity $activity, Schedule $schedule, string $type): array
    {
        // format tanggal & jam (WIB)
        $dateStr = null;
        try {
            $dateStr = \Carbon\Carbon::parse($schedule->date . ' ' . $schedule->time, 'Asia/Jakarta')
                ->translatedFormat('l, d F Y • H:i') . ' WIB';
        } catch (\Throwable $e) {
            $dateStr = ($schedule->date ?? '-') . ' ' . ($schedule->time ?? '-');
        }

        // subject dinamis
        $subjectBase = match ($type) {
            'created' => 'Jadwal Baru Dibuat',
            'updated' => 'Jadwal Diperbarui',
            'deleted' => 'Jadwal Dibatalkan',
            default   => 'Notifikasi Jadwal',
        };

        // URL detail (sesuaikan rute FE kamu)
        $url = url('/app/schedule/' . ($schedule->id ?? ''));

        return [
            'subject'        => $subjectBase,
            'app_name'       => config('app.name'),
            'activity_name'  => $activity->name ?? 'Aktivitas',
            'tracking_code'  => $activity->tracking_code ?? '-',
            'notary_name'    => optional(User::find($activity->user_notaris_id))->name ?? '-',
            'place'          => $schedule->location ?? null,
            'date_str'       => $dateStr,
            'notes'          => $schedule->notes ?? null,
            'type'           => $type, // created|updated|deleted
            'url'            => $type === 'deleted' ? null : $url, // jika sudah dihapus, tidak perlu URL
        ];
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
