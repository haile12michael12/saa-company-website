<?php

namespace App\Services\Subscription;

use App\Models\Company;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function getPlans(): array|\Illuminate\Database\Eloquent\Collection
    {
        return SubscriptionPlan::where('is_active', true)->get();
    }

    public function createPlan(array $data): SubscriptionPlan
    {
        return SubscriptionPlan::create([
            'name' => $data['name'],
            'price' => $data['price'] ?? 0,
            'billing_interval' => $data['billing_interval'] ?? 'monthly',
            'features' => $data['features'] ?? [],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updatePlan(SubscriptionPlan $plan, array $data): SubscriptionPlan
    {
        $plan->update($data);
        return $plan;
    }

    public function subscribe(Company $company, SubscriptionPlan $plan, array $options = []): Subscription
    {
        return DB::transaction(function () use ($company, $plan, $options) {
            // Cancel any existing active subscriptions
            Subscription::where('company_id', $company->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled', 'ends_at' => now()]);

            $trialDays = $options['trial_days'] ?? 14;
            $interval = $plan->billing_interval === 'yearly' ? 365 : 30;

            return Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays($interval),
                'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
                'gateway' => $options['gateway'] ?? 'manual',
                'gateway_id' => $options['gateway_id'] ?? null,
            ]);
        });
    }

    public function changePlan(Subscription $subscription, SubscriptionPlan $newPlan): Subscription
    {
        $interval = $newPlan->billing_interval === 'yearly' ? 365 : 30;
        $subscription->update([
            'plan_id' => $newPlan->id,
            'ends_at' => now()->addDays($interval),
        ]);

        return $subscription;
    }

    public function cancelSubscription(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);

        return $subscription;
    }

    public function checkFeature($company, string $featureKey): bool
    {
        $companyId = $company instanceof Company ? $company->id : (is_numeric($company) ? $company : null);
        if (!$companyId) {
            return true; // Default fallback for system/admin without company scope
        }

        $subscription = Subscription::with('plan')
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->plan->hasFeature($featureKey);
    }

    public function getActiveSubscription($company): ?Subscription
    {
        $companyId = $company instanceof Company ? $company->id : (is_numeric($company) ? $company : null);
        if (!$companyId) return null;

        return Subscription::with('plan')
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->latest()
            ->first();
    }
}
