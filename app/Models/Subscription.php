<?php

namespace App\Models;

use App\Models\Subscription\Subscription as BaseSubscription;

class Subscription extends BaseSubscription
{
    protected $table = 'subscriptions';
}
