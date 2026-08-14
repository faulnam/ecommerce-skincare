<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly array $payload
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Contact Hijab] ' . ($this->payload['subject'] ?? 'Pesan Baru'),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    $this->payload['email'] ?? config('mail.from.address'),
                    $this->payload['name'] ?? 'Pengunjung'
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'payload' => $this->payload,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
