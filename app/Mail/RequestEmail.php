<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $data;
    protected $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $data)
    {
        $this->data = $data;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.MAIL_FROM_ADDRESS'), '[REQUEST-GATE]')
            ->subject($this->data['title'])
            ->view('emails.request_email')
            ->with([
                'content' => $this->content,
                'category' => $this->data['category']
            ]);
    }
}
