<?php

namespace App\Policies;

use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, mixed $invoice): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, mixed $invoice): bool
    {
        return false;
    }

    public function delete(User $user, mixed $invoice): bool
    {
        return false;
    }

    public function restore(User $user, mixed $invoice): bool
    {
        return false;
    }

    public function forceDelete(User $user, mixed $invoice): bool
    {
        return false;
    }
}