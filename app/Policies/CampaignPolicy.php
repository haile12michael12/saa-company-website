<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return empty($user->company_id) || $user->company_id === $campaign->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return empty($user->company_id) || $user->company_id === $campaign->company_id;
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return empty($user->company_id) || $user->company_id === $campaign->company_id;
    }
}
