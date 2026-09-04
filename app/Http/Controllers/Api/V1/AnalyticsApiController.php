<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AI\BusinessInsightService;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsApiController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected BusinessInsightService $biService
    ) {}

    public function metrics(Request $request)
    {
        $companyId = $request->user()?->company_id;
        $days = (int)$request->get('days', 30);

        return response()->json($this->analyticsService->getMetricsSummary($companyId, $days));
    }

    public function businessIntelligence(Request $request)
    {
        $companyId = $request->user()?->company_id;
        return response()->json($this->biService->generateExecutiveSummary($companyId));
    }
}
