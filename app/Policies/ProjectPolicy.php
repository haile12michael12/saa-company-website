<?php

namespace App\Policies;

use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, mixed $project): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, mixed $project): bool
    {
        return false;
    }

    public function delete(User $user, mixed $project): bool
    {
        return false;
    }

    public function restore(User $user, mixed $project): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $project): bool
    {
        return false;
    }
}