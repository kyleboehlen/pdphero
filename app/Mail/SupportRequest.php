<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Models
use App\Models\User\User;

class SupportRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $body)
    {
        $this->user = $user;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to(config('support.email.to.email'), config('support.email.to.name'))
            ->replyTo($this->user->email, $this->user->name)
            ->cc($this->user->email, $this->user->name)
            ->subject('Support Request For: ' . $this->user->name)
            ->view('support.mail.html.support-request')
            ->text('support.mail.text.support-request');
    }
}
