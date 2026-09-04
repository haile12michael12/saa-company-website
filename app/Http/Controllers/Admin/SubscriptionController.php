<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionPlan;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function index(Request $request)
    {
        $plans = $this->subscriptionService->getPlans();
        $currentSubscription = $this->subscriptionService->getActiveSubscription(auth()->user()->company_id);

        if ($request->wantsJson()) {
            return response()->json([
                'plans' => $plans,
                'current_subscription' => $currentSubscription,
            ]);
        }

        if (view()->exists('admin.subscriptions.index')) {
            return view('admin.subscriptions.index', compact('plans', 'currentSubscription'));
        }

        return response()->json([
            'plans' => $plans,
            'current_subscription' => $currentSubscription,
        ]);
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $company = auth()->user()->company ?? Company::first();

        if (!$company) {
            return response()->json(['message' => 'No associated company found.'], 422);
        }

        $subscription = $this->subscriptionService->subscribe($company, $plan, [
            'gateway' => $request->get('gateway', 'manual'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Subscribed to plan successfully.', 'subscription' => $subscription], 201);
        }

        toastr()->success("Subscribed to plan '{$plan->name}' successfully!");
        return redirect()->back();
    }

    public function cancel(Request $request)
    {
        $subscription = $this->subscriptionService->getActiveSubscription(auth()->user()->company_id);

        if ($subscription) {
            $this->subscriptionService->cancelSubscription($subscription);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Subscription cancelled.']);
        }

        toastr()->success('Subscription cancelled.');
        return redirect()->back();
    }
}