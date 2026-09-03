<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public mixed $lead = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Lead notification');
    }

    public function content(): Content
    {
        return new Content(html: '<p>A lead notification is ready.</p>');
    }
}