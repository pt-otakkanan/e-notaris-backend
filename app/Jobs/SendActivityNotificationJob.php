<?php

namespace App\Jobs;

use App\Mail\ClientActivityMail;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendActivityNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 180; // ✅ 3 minutes
    public $backoff = [60, 120, 300]; // ✅ More aggressive backoff
    public $maxExceptions = 3; // ✅ Max exceptions before failing

    protected $userId;
    protected $activityId;
    protected $type;

    public function __construct(int $userId, int $activityId, string $type)
    {
        $this->userId = $userId;
        $this->activityId = $activityId;
        $this->type = $type;
    }

    public function handle()
    {
        $startTime = microtime(true);

        try {
            // ✅ Cast to int and find with timeout awareness
            $client = User::find($this->userId);
            $activity = Activity::with(['notaris', 'schedules'])->find($this->activityId);

            if (!$client || !$activity) {
                Log::warning('Client or Activity not found', [
                    'user_id' => $this->userId,
                    'activity_id' => $this->activityId,
                ]);

                // ✅ Don't retry if data doesn't exist
                $this->delete();
                return;
            }

            // Build email
            $details = $this->buildActivityMailDetails($client, $activity, $this->type);
            $subject = $details['subject'] . ' - ' . ($details['activity_name'] ?? 'Aktivitas');

            // ✅ Set mail timeout
            config(['mail.timeout' => 45]);

            $notary = $activity->notaris;
            $mailable = new ClientActivityMail($details, $subject);

            // ✅ Send with timeout protection
            $mailer = Mail::to($client->email, $client->name);
            if ($notary?->email) {
                $mailer->bcc($notary->email, $notary->name);
            }

            $mailer->send($mailable);

            $duration = round(microtime(true) - $startTime, 2);

            Log::info('Email sent successfully via queue', [
                'user_id' => $this->userId,
                'activity_id' => $this->activityId,
                'type' => $this->type,
                'duration_seconds' => $duration,
                'attempt' => $this->attempts(),
            ]);
        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2);

            Log::error('Queue email failed', [
                'user_id' => $this->userId,
                'activity_id' => $this->activityId,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'duration_seconds' => $duration,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            // ✅ Release back to queue with backoff
            if ($this->attempts() < $this->tries) {
                $delay = $this->backoff[$this->attempts() - 1] ?? 60;
                $this->release($delay);
            } else {
                throw $e; // Final attempt, let it fail
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Email job permanently failed', [
            'user_id' => $this->userId,
            'activity_id' => $this->activityId,
            'type' => $this->type,
            'error' => $exception->getMessage(),
            'error_class' => get_class($exception),
        ]);

        // ✅ Optional: Notify admin about failed email
        // \App\Models\FailedEmailLog::create([...]);
    }

    private function buildActivityMailDetails(User $client, Activity $activity, string $type): array
    {
        // ... your existing code
        $notary = $activity->notaris;
        $schedule = $activity->schedules[0] ?? null;
        $place = $schedule->place ?? ($activity->notaris->city ?? null) ?? null;

        $dateStr = null;
        if (!empty($schedule?->datetime)) {
            try {
                $dateStr = \Carbon\Carbon::parse($schedule->datetime)
                    ->locale('id')
                    ->translatedFormat('d M Y, H:i');
            } catch (\Throwable $e) {
                $dateStr = null;
            }
        }

        $frontend = rtrim(config('app.frontend_url'), '/');
        $url = $frontend . '/app/activity/' . $activity->id;

        return [
            'type' => $type,
            'subject' => $type === 'added'
                ? 'Anda Ditambahkan ke Aktivitas'
                : 'Anda Dihapus dari Aktivitas',
            'app_name' => config('app.name'),
            'client_name' => $client->name,
            'client_email' => $client->email,
            'activity_id' => $activity->id,
            'activity_name' => $activity->name ?? 'Aktivitas',
            'tracking_code' => $activity->tracking_code ?? '-',
            'notary_name' => $notary?->name ?? '-',
            'place' => $place,
            'date_str' => $dateStr,
            'url' => $url,
        ];
    }
}
