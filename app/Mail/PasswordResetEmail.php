<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Mail to send the reset link to the user.
 *
 * @package App\Mail
 */
class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The password reset token.
     *
     * @var string
     */
    private $token;

    /**
     * Create a new message instance.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('layouts.email.reset')
            ->subject('MovieTracker - Password reset request')
            ->from('noreply@deflox.net')
            ->with([
                'token' => $this->token,
            ]);
    }
}
