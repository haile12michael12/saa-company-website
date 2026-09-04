<?php

namespace App\Services\Booking;

use App\Events\AppointmentBooked;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\AppointmentNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function getAppointmentsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Appointment::with(['customer', 'user']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('starts_at', $filters['date']);
        }

        return $query->latest('starts_at')->paginate($filters['per_page'] ?? 15);
    }

    public function checkConflict(?int $companyId, Carbon|string $startsAt, Carbon|string $endsAt, ?int $excludeId = null, ?int $userId = null): bool
    {
        $start = Carbon::parse($startsAt);
        $end = Carbon::parse($endsAt);

        $query = Appointment::where('status', '!=', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                  ->orWhereBetween('ends_at', [$start, $end])
                  ->orWhere(function ($sub) use ($start, $end) {
                      $sub->where('starts_at', '<=', $start)
                          ->where('ends_at', '>=', $end);
                  });
            });

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function createAppointment(array $data, ?int $companyId = null, ?User $creator = null): Appointment
    {
        $companyId = $companyId ?? ($creator?->company_id ?? null);
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = isset($data['ends_at']) ? Carbon::parse($data['ends_at']) : $startsAt->copy()->addHour();

        if ($this->checkConflict($companyId, $startsAt, $endsAt, null, $data['user_id'] ?? null)) {
            throw ValidationException::withMessages([
                'starts_at' => ['An appointment is already scheduled during this timeframe.'],
            ]);
        }

        return DB::transaction(function () use ($data, $companyId, $startsAt, $endsAt, $creator) {
            $appointment = Appointment::create([
                'company_id' => $companyId,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $data['user_id'] ?? ($creator?->id ?? null),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $data['status'] ?? 'pending',
                'location' => $data['location'] ?? 'online',
            ]);

            event(new AppointmentBooked($appointment));

            if ($appointment->customer && $appointment->customer->user) {
                $appointment->customer->user->notify(new AppointmentNotification($appointment, 'booked'));
            }

            return $appointment;
        });
    }

    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        $startsAt = isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : $appointment->starts_at;
        $endsAt = isset($data['ends_at']) ? Carbon::parse($data['ends_at']) : (isset($data['starts_at']) ? $startsAt->copy()->addHour() : $appointment->ends_at);

        if (isset($data['starts_at']) && $this->checkConflict($appointment->company_id, $startsAt, $endsAt, $appointment->id, $data['user_id'] ?? $appointment->user_id)) {
            throw ValidationException::withMessages([
                'starts_at' => ['An appointment is already scheduled during this timeframe.'],
            ]);
        }

        $appointment->update(array_merge($data, [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]));

        return $appointment;
    }

    public function cancelAppointment(Appointment $appointment, ?string $reason = null): Appointment
    {
        $appointment->update([
            'status' => 'cancelled',
            'description' => $reason ? ($appointment->description . "\nCancelled reason: " . $reason) : $appointment->description,
        ]);

        if ($appointment->customer && $appointment->customer->user) {
            $appointment->customer->user->notify(new AppointmentNotification($appointment, 'cancelled'));
        }

        return $appointment;
    }

    public function getAvailableSlots(?int $companyId, string $date, int $durationMinutes = 60): array
    {
        $day = Carbon::parse($date);
        $startWork = $day->copy()->setHour(9)->setMinute(0)->setSecond(0);
        $endWork = $day->copy()->setHour(17)->setMinute(0)->setSecond(0);

        $slots = [];
        $current = $startWork->copy();

        while ($current->copy()->addMinutes($durationMinutes)->lte($endWork)) {
            $slotEnd = $current->copy()->addMinutes($durationMinutes);
            $isBusy = $this->checkConflict($companyId, $current, $slotEnd);

            $slots[] = [
                'start' => $current->toIso8601String(),
                'end' => $slotEnd->toIso8601String(),
                'available' => !$isBusy,
                'display' => $current->format('H:i') . ' - ' . $slotEnd->format('H:i'),
            ];

            $current->addMinutes($durationMinutes);
        }

        return $slots;
    }
}