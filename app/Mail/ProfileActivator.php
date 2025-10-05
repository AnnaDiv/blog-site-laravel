<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileActivator extends Mailable
{
    use Queueable, SerializesModels;

    public $activateProfileUrl;
    /**
     * Create a new message instance.
     */
    public function __construct(string $token)
    {
        $this->activateProfileUrl = url(route('account.activate', [
            'token' => urlencode($token)
        ], false));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activate your profile',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activate-profile',
            with: [
                'url' => $this->activateProfileUrl
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
