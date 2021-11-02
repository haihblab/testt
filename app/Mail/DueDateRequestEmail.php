<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DueDateRequestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $request;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Due-Date-Request: ' . $this->request->name . ' / ' . $this->request->due_date;
        return $this->from(config('mail.MAIL_FROM_ADDRESS'), '[REQUEST-GATE]')
            ->subject($subject)
            ->view('emails.due_date_request')
            ->with([
                'request' => $this->request
            ]);
    }
}
