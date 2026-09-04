<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AI\BusinessInsightService;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected BusinessInsightService $biService
    ) {}

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $days = (int)$request->get('days', 30);

        $metrics = $this->analyticsService->getMetricsSummary($companyId, $days);
        $biReport = $this->biService->generateExecutiveSummary($companyId);

        if ($request->wantsJson()) {
            return response()->json([
                'metrics' => $metrics,
                'business_intelligence' => $biReport,
            ]);
        }

        if (view()->exists('admin.analytics.index')) {
            return view('admin.analytics.index', compact('metrics', 'biReport', 'days'));
        }

        return response()->json([
            'metrics' => $metrics,
            'business_intelligence' => $biReport,
        ]);
    }

    public function aggregate(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $date = $request->get('date', now()->toDateString());

        $result = $this->analyticsService->aggregateDaily($companyId, $date);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Analytics aggregated successfully.', 'result' => $result]);
        }

        toastr()->success('Daily metrics updated.');
        return redirect()->back();
    }
}
