<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Models\Campaign;
use App\Services\Marketing\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(protected CampaignService $campaignService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Campaign::class);

        $campaigns = $this->campaignService->getCampaignsForCompany(
            auth()->user()->company_id,
            $request->only(['status', 'search', 'per_page'])
        );

        if (view()->exists('admin.marketing.index')) {
            return view('admin.marketing.index', compact('campaigns'));
        }

        return response()->json($campaigns);
    }

    public function store(StoreCampaignRequest $request)
    {
        $this->authorize('create', Campaign::class);

        $campaign = $this->campaignService->createCampaign(
            $request->validated(),
            auth()->user()->company_id,
            auth()->user()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Marketing campaign created.', 'campaign' => $campaign], 201);
        }

        toastr()->success('Marketing campaign created.');
        return redirect()->route('admin.campaigns.show', $campaign);
    }

    public function show(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $campaign->load(['recipients', 'creator']);

        if (request()->wantsJson()) {
            return response()->json($campaign);
        }

        return view('admin.marketing.show', compact('campaign'));
    }

    public function sendCampaign(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $this->campaignService->dispatchCampaign($campaign);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Campaign queued for dispatch.']);
        }

        toastr()->success('Campaign queued for delivery.');
        return redirect()->back();
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        toastr()->success('Campaign deleted.');
        return redirect()->route('admin.campaigns.index');
    }
}