<?php

namespace App\Services;

use App\Models\Quote;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

class QuotePdfService
{
    /**
     * Generate binary PDF string for the given quote.
     */
    public function generatePdfContent(Quote $quote): string
    {
        $quote->loadMissing(['items', 'customer', 'lead', 'company']);
        $html = view('admin.sales.quotes.pdf', compact('quote'))->render();

        if (class_exists(Dompdf::class)) {
            $options = new Options();
            $options->set('defaultFont', 'Helvetica');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return $dompdf->output();
        }

        return $html;
    }

    /**
     * Return a downloadable PDF response.
     */
    public function downloadResponse(Quote $quote, ?string $filename = null): Response
    {
        $filename = $filename ?: 'Quote-' . ($quote->number ?: $quote->id) . '.pdf';
        $content = $this->generatePdfContent($quote);

        if (str_starts_with($content, '%PDF')) {
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // Return HTML response if binary PDF engine is unavailable
        return response($content, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * Return an inline stream response for viewing in browser.
     */
    public function streamResponse(Quote $quote, ?string $filename = null): Response
    {
        $filename = $filename ?: 'Quote-' . ($quote->number ?: $quote->id) . '.pdf';
        $content = $this->generatePdfContent($quote);

        if (str_starts_with($content, '%PDF')) {
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        return response($content, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
