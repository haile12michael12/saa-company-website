<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Quote;
use App\Services\AI\AIService;
use App\Services\AI\BusinessInsightService;
use App\Services\AI\LeadScoringService;
use App\Services\AI\RecommendationService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(
        protected AIService $aiService,
        protected LeadScoringService $scoringService,
        protected BusinessInsightService $biService,
        protected RecommendationService $recommendationService
    ) {}

    public function index()
    {
        $insights = $this->biService->generateExecutiveSummary(auth()->user()->company_id);
        $recentLeads = Lead::where('company_id', auth()->user()->company_id)->latest()->take(10)->get();

        if (view()->exists('admin.ai.index')) {
            return view('admin.ai.index', compact('insights', 'recentLeads'));
        }

        return response()->json(['insights' => $insights, 'leads' => $recentLeads]);
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $response = $this->aiService->generateAssistantResponse(
            $request->message,
            ['company_id' => auth()->user()?->company_id]
        );

        return response()->json($response);
    }

    public function scoreLead(Lead $lead)
    {
        $result = $this->scoringService->calculateScore($lead);
        $this->scoringService->scoreAndUpdateLead($lead);

        return response()->json([
            'message' => 'Lead scored successfully.',
            'score' => $result['score'],
            'grade' => $result['grade'],
            'breakdown' => $result['breakdown'],
        ]);
    }

    public function generateProposalSummary(Quote $quote)
    {
        $summary = $this->aiService->generateProposalSummary($quote);

        return response()->json([
            'quote_id' => $quote->id,
            'summary' => $summary,
        ]);
    }
}
