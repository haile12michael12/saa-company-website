<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionPlanRequest;
use App\Models\Subscription\SubscriptionPlan;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function index()
    {
        $plans = $this->subscriptionService->getPlans();
        return response()->json($plans);
    }

    public function store(StoreSubscriptionPlanRequest $request)
    {
        $plan = $this->subscriptionService->createPlan($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Subscription plan created.', 'plan' => $plan], 201);
        }

        toastr()->success('Plan created.');
        return redirect()->back();
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $updated = $this->subscriptionService->updatePlan($plan, $request->all());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Plan updated.', 'plan' => $updated]);
        }

        toastr()->success('Plan updated.');
        return redirect()->back();
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Plan deleted.']);
        }

        toastr()->success('Plan deleted.');
        return redirect()->back();
    }
}