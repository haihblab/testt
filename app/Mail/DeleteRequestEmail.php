<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteRequestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Delete-Request: ' . $this->data['request'] . ' - ' . $this->data['deleted_at'];
        return $this->from(config('mail.MAIL_FROM_ADDRESS'), '[REQUEST-GATE]')
            ->subject($subject)
            ->view('emails.delete_request_email')
            ->with([
                'data' => $this->data
            ]);
    }
}
