<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $user;
    public $expiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code, User $user)
    {
        $this->code = $code;
        $this->user = $user;
        $this->expiresAt = $user->two_factor_expires_at;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MDRRMO Security Code - Action Required',
            from: config('mail.from.address', 'noreply@bukidnonalert.gov.ph'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor',
            with: [
                'code' => $this->code,
                'user' => $this->user,
                'expiresAt' => $this->expiresAt,
                'expiresInMinutes' => $this->expiresAt ? $this->expiresAt->diffInMinutes(now()) : 5,
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
