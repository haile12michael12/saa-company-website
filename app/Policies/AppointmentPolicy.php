<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return empty($user->company_id) || $user->company_id === $appointment->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return empty($user->company_id) || $user->company_id === $appointment->company_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return empty($user->company_id) || $user->company_id === $appointment->company_id;
    }
}
