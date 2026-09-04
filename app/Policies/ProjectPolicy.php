<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    protected function matchesCompany(User $user, ?Project $project = null): bool
    {
        if (empty($user->company_id)) {
            return true;
        }

        if ($project && !empty($project->company_id)) {
            return $user->company_id === $project->company_id;
        }

        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $this->matchesCompany($user, $project);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $this->matchesCompany($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->matchesCompany($user, $project);
    }
}