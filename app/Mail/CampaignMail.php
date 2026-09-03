<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public mixed $campaign = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Campaign message');
    }

    public function content(): Content
    {
        return new Content(html: '<p>A campaign message is ready.</p>');
    }
}