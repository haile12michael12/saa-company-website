<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workflow;

class WorkflowPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Workflow $workflow): bool
    {
        return empty($user->company_id) || $user->company_id === $workflow->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Workflow $workflow): bool
    {
        return empty($user->company_id) || $user->company_id === $workflow->company_id;
    }

    public function delete(User $user, Workflow $workflow): bool
    {
        return empty($user->company_id) || $user->company_id === $workflow->company_id;
    }
}
