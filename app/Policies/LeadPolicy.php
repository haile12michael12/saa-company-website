<?php

namespace App\Policies;

use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, mixed $lead): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, mixed $lead): bool
    {
        return false;
    }

    public function delete(User $user, mixed $lead): bool
    {
        return false;
    }

    public function restore(User $user, mixed $lead): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $lead): bool
    {
        return false;
    }
}