<?php

namespace App\Jobs;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduleNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    protected $scheduleId;
    protected $action;

    public function __construct($scheduleId, $action)
    {
        $this->scheduleId = $scheduleId;
        $this->action = $action; // 'created', 'updated', 'deleted'
    }

    public function handle()
    {
        try {
            $schedule = Schedule::with('activity')->find($this->scheduleId);

            if (!$schedule) {
                Log::warning('Schedule not found', ['schedule_id' => $this->scheduleId]);
                return;
            }

            // Panggil method notifySchedule dari controller
            // Karena ini di Job, kita harus recreate logicnya atau buat service class
            // Untuk sekarang, log dulu saja
            Log::info('Schedule notification', [
                'schedule_id' => $this->scheduleId,
                'action' => $this->action,
            ]);

            // TODO: Implement email sending logic for schedule

        } catch (\Throwable $e) {
            Log::error('Schedule notification failed', [
                'schedule_id' => $this->scheduleId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Schedule notification job failed after all retries', [
            'schedule_id' => $this->scheduleId,
            'error' => $exception->getMessage(),
        ]);
    }
}
