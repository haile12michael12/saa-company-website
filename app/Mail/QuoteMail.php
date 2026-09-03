<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public mixed $quote = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Quote');
    }

    public function content(): Content
    {
        return new Content(html: '<p>Your quote is ready.</p>');
    }
}