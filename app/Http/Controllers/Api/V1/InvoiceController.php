<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        $invoices = Invoice::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->with(['customer', 'items'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($invoices);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['customer', 'items', 'payments']));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'sometimes|required|string|in:draft,sent,paid,partial,overdue,cancelled',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted.']);
    }
}