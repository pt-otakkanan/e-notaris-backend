<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $details;
    public string $subjectLine;

    /**
     * Create a new message instance.
     *
     * @param array $details
     * @param string $subjectLine
     */
    public function __construct(array $details, string $subjectLine)
    {
        $this->details = $details;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('mail.general_notification');
    }
}
