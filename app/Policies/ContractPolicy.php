<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $contract): bool
    {
        return empty($user->company_id) || $user->company_id === $contract->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contract $contract): bool
    {
        return empty($user->company_id) || $user->company_id === $contract->company_id;
    }

    public function delete(User $user, Contract $contract): bool
    {
        return empty($user->company_id) || $user->company_id === $contract->company_id;
    }
}
