<?php

namespace App\Models;

use App\Models\Subscription\SubscriptionPlan as BaseSubscriptionPlan;

class SubscriptionPlan extends BaseSubscriptionPlan
{
    protected $table = 'subscription_plans';
}
