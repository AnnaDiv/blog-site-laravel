<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\models\User;

class ResetPasswordSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $passwordResetUrl;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(string $token, User $user)
    {
        $this->user = $user;
        $this->passwordResetUrl = url(route('password.reset', [
            'token' => urlencode($token)
        ], false));

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Submission',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'url' => $this->passwordResetUrl,
                'user' => $this->user
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
