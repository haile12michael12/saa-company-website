<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Services\QuotePdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicQuoteController extends Controller
{
    public function show(string $token)
    {
        $quote = Quote::where('token', $token)
            ->with(['items', 'customer', 'lead', 'proposals'])
            ->firstOrFail();

        return view('frontend.quote-portal', compact('quote'));
    }

    public function accept(Request $request, string $token)
    {
        $quote = Quote::where('token', $token)->firstOrFail();

        if ($quote->isExpired()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'This quotation has expired.'], 422);
            }
            return redirect()->back()->with('error', 'This quotation has expired and can no longer be accepted.');
        }

        $validated = $request->validate([
            'signer_name' => ['required', 'string', 'max:200'],
            'agreement' => ['required', 'accepted'],
        ]);

        $quote->update([
            'status' => 'accepted',
            'accepted_at' => Carbon::now(),
            'notes' => ($quote->notes ? $quote->notes . "\n\n" : '') . "Digitally accepted by: {$validated['signer_name']} on " . Carbon::now()->toDayDateTimeString(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Quotation #{$quote->number} has been officially accepted! Thank you.",
            ]);
        }

        return redirect()->back()->with('success', "Quotation #{$quote->number} has been officially accepted! Our team has been notified and will initiate project onboarding shortly.");
    }

    public function reject(Request $request, string $token)
    {
        $quote = Quote::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $quote->update([
            'status' => 'rejected',
            'rejected_at' => Carbon::now(),
            'rejection_reason' => $validated['reason'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your feedback. The quotation has been marked as declined.',
            ]);
        }

        return redirect()->back()->with('info', 'Thank you for your feedback. We appreciate your review.');
    }

    public function downloadPdf(string $token, QuotePdfService $pdfService)
    {
        $quote = Quote::where('token', $token)->firstOrFail();

        return $pdfService->downloadResponse($quote);
    }
}

