<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Webhook;

class WebhookPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Webhook $webhook): bool
    {
        return empty($user->company_id) || $user->company_id === $webhook->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Webhook $webhook): bool
    {
        return empty($user->company_id) || $user->company_id === $webhook->company_id;
    }

    public function delete(User $user, Webhook $webhook): bool
    {
        return empty($user->company_id) || $user->company_id === $webhook->company_id;
    }
}
