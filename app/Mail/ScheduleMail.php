<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $details;
    public string $subjectLine;

    public function __construct(array $details, string $subjectLine)
    {
        $this->details     = $details;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('mail.schedule');
    }

    public function attachments(): array
    {
        return [];
    }
}
