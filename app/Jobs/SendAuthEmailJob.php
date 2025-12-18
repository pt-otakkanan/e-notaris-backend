<?php

namespace App\Jobs;

use App\Mail\AuthMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAuthEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 180; // 3 minutes
    public $backoff = [60, 120, 300]; // More aggressive backoff
    public $maxExceptions = 3;

    protected $userId;
    protected $emailType; // 'verification' or 'reset_password'
    protected $emailData;

    /**
     * @param int $userId
     * @param string $emailType - 'verification' or 'reset_password'
     * @param array $emailData - Data yang diperlukan untuk email
     */
    public function __construct(int $userId, string $emailType, array $emailData)
    {
        $this->userId = $userId;
        $this->emailType = $emailType;
        $this->emailData = $emailData;
    }

    public function handle()
    {
        $startTime = microtime(true);

        try {
            // Find user
            $user = User::find($this->userId);

            if (!$user) {
                Log::warning('User not found for auth email', [
                    'user_id' => $this->userId,
                    'email_type' => $this->emailType,
                ]);

                // Don't retry if user doesn't exist
                $this->delete();
                return;
            }

            // Set mail timeout
            config(['mail.timeout' => 45]);

            // Send email based on type
            if ($this->emailType === 'verification') {
                $this->sendVerificationEmail($user);
            } elseif ($this->emailType === 'reset_password') {
                $this->sendResetPasswordEmail($user);
            } else {
                Log::error('Invalid email type', [
                    'user_id' => $this->userId,
                    'email_type' => $this->emailType,
                ]);
                $this->delete();
                return;
            }

            $duration = round(microtime(true) - $startTime, 2);

            Log::info('Auth email sent successfully via queue', [
                'user_id' => $this->userId,
                'email_type' => $this->emailType,
                'duration_seconds' => $duration,
                'attempt' => $this->attempts(),
            ]);
        } catch (\Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2);

            Log::error('Queue auth email failed', [
                'user_id' => $this->userId,
                'email_type' => $this->emailType,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'duration_seconds' => $duration,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            // Release back to queue with backoff
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
        Log::error('Auth email job permanently failed', [
            'user_id' => $this->userId,
            'email_type' => $this->emailType,
            'error' => $exception->getMessage(),
            'error_class' => get_class($exception),
        ]);

        // Optional: Create notification for admin or retry manually
        // \App\Models\FailedEmailLog::create([...]);
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(User $user): void
    {
        $frontend = rtrim(config('app.frontend_url'), '/');
        $verifyUrl = $frontend . '/verify-code?' . http_build_query([
            'email' => $user->email,
            'code'  => $this->emailData['code'],
        ]);

        $details = [
            'name'       => $user->name,
            'role_label' => $user->role_id == 3 ? 'Notaris' : 'Penghadap',
            'website'    => config('app.name'),
            'kode'       => $this->emailData['code'],
            'url'        => $verifyUrl,
            'expires_at' => $this->emailData['expires_at'],
        ];

        Mail::to($user->email)->send(new AuthMail($details));
    }

    /**
     * Send reset password email
     */
    private function sendResetPasswordEmail(User $user): void
    {
        $frontend = rtrim(config('app.frontend_url'), '/');
        $resetUrl = $frontend . '/reset-password?' . http_build_query([
            'email' => $user->email,
            'token' => $this->emailData['token'],
        ]);

        $details = [
            'name'       => $user->name,
            'website'    => config('app.name'),
            'url'        => $resetUrl,
            'expires_at' => $this->emailData['expires_at'],
        ];

        Mail::to($user->email)->send(new ResetPasswordMail($details));
    }
}
