<?php

namespace App\Http\Middleware;

use App\Services\Subscription\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionFeature
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if ($user && $user->company_id) {
            if (!$this->subscriptionService->checkFeature($user->company_id, $feature)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "The feature '{$feature}' is not available on your current plan. Please upgrade.",
                    ], 403);
                }

                toastr()->error("The feature '{$feature}' is not included in your current subscription plan. Please upgrade.");
                return redirect()->route('admin.subscriptions.index');
            }
        }

        return $next($request);
    }
}
