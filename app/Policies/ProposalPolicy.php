<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    protected function matchesCompany(User $user, ?Proposal $proposal = null): bool
    {
        if (empty($user->company_id)) {
            return true;
        }

        if ($proposal && !empty($proposal->company_id)) {
            return $user->company_id === $proposal->company_id;
        }

        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Proposal $proposal): bool
    {
        return $this->matchesCompany($user, $proposal);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Proposal $proposal): bool
    {
        return $this->matchesCompany($user, $proposal);
    }

    public function delete(User $user, Proposal $proposal): bool
    {
        return $this->matchesCompany($user, $proposal);
    }
}
