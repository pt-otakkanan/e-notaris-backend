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

    public $tries = 3; // Retry 3x jika gagal
    public $timeout = 60; // Timeout 60 detik
    public $backoff = [10, 30, 60]; // Retry delay

    protected $userId;
    protected $activityId;
    protected $type;

    public function __construct($userId, $activityId, $type)
    {
        $this->userId = $userId;
        $this->activityId = $activityId;
        $this->type = $type; // 'added' or 'removed'
    }

    public function handle()
    {
        try {
            $client = User::find($this->userId);
            $activity = Activity::with(['notaris', 'schedules'])->find($this->activityId);

            if (!$client || !$activity) {
                Log::warning('Client or Activity not found', [
                    'user_id' => $this->userId,
                    'activity_id' => $this->activityId,
                ]);
                return;
            }

            // Build email details
            $details = $this->buildActivityMailDetails($client, $activity, $this->type);
            $subject = $details['subject'] . ' - ' . ($details['activity_name'] ?? 'Aktivitas');

            // Send email
            $notary = $activity->notaris;
            $mailable = new ClientActivityMail($details, $subject);

            $mailer = Mail::to($client->email, $client->name);
            if ($notary?->email) {
                $mailer->bcc($notary->email, $notary->name);
            }
            $mailer->send($mailable);

            Log::info('Email sent successfully via queue', [
                'user_id' => $this->userId,
                'activity_id' => $this->activityId,
                'type' => $this->type,
            ]);
        } catch (\Throwable $e) {
            Log::error('Queue email failed', [
                'user_id' => $this->userId,
                'activity_id' => $this->activityId,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Re-throw untuk retry
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Email job failed after all retries', [
            'user_id' => $this->userId,
            'activity_id' => $this->activityId,
            'error' => $exception->getMessage(),
        ]);
    }

    private function buildActivityMailDetails(User $client, Activity $activity, string $type): array
    {
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
