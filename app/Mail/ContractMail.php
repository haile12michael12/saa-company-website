<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public mixed $contract = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Contract');
    }

    public function content(): Content
    {
        return new Content(html: '<p>Your contract is ready.</p>');
    }
}