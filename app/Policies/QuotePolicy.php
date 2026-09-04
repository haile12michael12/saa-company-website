<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    protected function matchesCompany(User $user, ?Quote $quote = null): bool
    {
        if (empty($user->company_id)) {
            return true;
        }

        if ($quote && !empty($quote->company_id)) {
            return $user->company_id === $quote->company_id;
        }

        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote);
    }

    public function delete(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote);
    }

    public function approve(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote) && $quote->canBeApproved();
    }

    public function sendEmail(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote);
    }

    public function accept(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote);
    }

    public function reject(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote);
    }

    public function convertToCustomer(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote) && $quote->canBeConvertedToCustomer();
    }

    public function convertToProject(User $user, Quote $quote): bool
    {
        return $this->matchesCompany($user, $quote) && $quote->canBeConvertedToProject();
    }
}