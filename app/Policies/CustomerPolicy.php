<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    protected function matchesCompany(User $user, ?Customer $customer = null): bool
    {
        if (empty($user->company_id)) {
            return true;
        }

        if ($customer && !empty($customer->company_id)) {
            return $user->company_id === $customer->company_id;
        }

        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->matchesCompany($user, $customer);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->matchesCompany($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->matchesCompany($user, $customer);
    }
}