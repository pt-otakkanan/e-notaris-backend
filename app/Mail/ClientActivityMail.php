<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientActivityMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $details;
    public string $subjectLine;

    /**
     * @param array  $details     // data untuk blade (client, activity, type, url, etc.)
     * @param string $subjectLine // subjek email
     */
    public function __construct(array $details, string $subjectLine)
    {
        $this->details = $details;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('mail.client_activity');
    }

    public function attachments(): array
    {
        return [];
    }
}
