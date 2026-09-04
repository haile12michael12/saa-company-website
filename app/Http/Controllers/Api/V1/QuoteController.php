<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        $quotes = Quote::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->with(['customer', 'items'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($quotes);
    }

    public function show(Quote $quote)
    {
        return response()->json($quote->load(['customer', 'items', 'proposals', 'contract']));
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'sometimes|required|string|in:draft,pending,sent,accepted,rejected,expired',
            'notes' => 'nullable|string',
        ]);

        $quote->update($validated);

        return response()->json($quote);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return response()->json(['message' => 'Quote deleted.']);
    }
}