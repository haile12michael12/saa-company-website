<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\AI\LeadScoringService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(protected LeadScoringService $scoringService) {}

    public function index(Request $request)
    {
        $companyId = $request->user()?->company_id;
        $leads = Lead::when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $validated['company_id'] = $request->user()?->company_id;
        $lead = Lead::create($validated);

        $this->scoringService->scoreAndUpdateLead($lead);

        return response()->json($lead, 201);
    }

    public function show(Lead $lead)
    {
        return response()->json($lead->load(['customer', 'quotes']));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|string',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $lead->update($validated);
        $this->scoringService->scoreAndUpdateLead($lead);

        return response()->json($lead);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return response()->json(['message' => 'Lead deleted.']);
    }
}