<?php

namespace App\Mail;

use App\Models\Quote;
use App\Services\QuotePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public mixed $quote = null)
    {
    }
    public function __construct(
        public Quote $quote,
        public ?string $customMessage = null,
        public bool $attachPdf = true
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Quote');
        return new Envelope(
            subject: 'Quotation #' . $this->quote->number . ' from SAA Digital Solutions'
        );
    }

    public function content(): Content
    {
        return new Content(html: '<p>Your quote is ready.</p>');
        return new Content(
            view: 'mail.quote',
            with: [
                'quote' => $this->quote,
                'customMessage' => $this->customMessage,
            ]
        );
    }

    public function attachments(): array
    {
        if (!$this->attachPdf) {
            return [];
        }

        try {
            $pdfService = app(QuotePdfService::class);
            $pdfContent = $pdfService->generatePdfContent($this->quote);

            if (str_starts_with($pdfContent, '%PDF')) {
                return [
                    Attachment::fromData(fn () => $pdfContent, 'Quotation-' . $this->quote->number . '.pdf')
                        ->withMime('application/pdf'),
                ];
            }
        } catch (\Throwable $e) {
            // If PDF generation encounters an issue, email continues without failing
        }

        return [];
    }
}