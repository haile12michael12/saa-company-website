<?php

namespace Tests\Feature\Appointments;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use App\Services\Booking\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_appointment_via_service()
    {
        $company = Company::create(['name' => 'Test Company']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $customer = Customer::create(['company_id' => $company->id, 'name' => 'John Doe', 'email' => 'john@example.com']);

        $service = app(BookingService::class);
        $appointment = $service->createAppointment([
            'customer_id' => $customer->id,
            'title' => 'Project Kickoff Meeting',
            'starts_at' => now()->addDay()->setHour(10)->setMinute(0)->toDateTimeString(),
            'ends_at' => now()->addDay()->setHour(11)->setMinute(0)->toDateTimeString(),
            'status' => 'pending',
            'location' => 'Google Meet',
        ], $company->id, $user);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'title' => 'Project Kickoff Meeting',
            'company_id' => $company->id,
        ]);
    }

    public function test_conflict_detection_prevents_double_booking()
    {
        $company = Company::create(['name' => 'Test Company']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $customer = Customer::create(['company_id' => $company->id, 'name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $service = app(BookingService::class);
        $startTime = now()->addDays(2)->setHour(14)->setMinute(0);

        $service->createAppointment([
            'customer_id' => $customer->id,
            'title' => 'First Meeting',
            'starts_at' => $startTime->toDateTimeString(),
            'ends_at' => $startTime->copy()->addHour()->toDateTimeString(),
        ], $company->id, $user);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $service->createAppointment([
            'customer_id' => $customer->id,
            'title' => 'Conflicting Meeting',
            'starts_at' => $startTime->copy()->addMinutes(30)->toDateTimeString(),
            'ends_at' => $startTime->copy()->addMinutes(90)->toDateTimeString(),
        ], $company->id, $user);
    }

    public function test_admin_can_schedule_and_view_appointments()
    {
        $company = Company::create(['name' => 'Test Company']);
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)->get(route('admin.appointments.index'), ['Accept' => 'application/json']);
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post(route('admin.appointments.store'), [
            'title' => 'Consultation Call',
            'starts_at' => now()->addDays(3)->setHour(10)->toDateTimeString(),
            'ends_at' => now()->addDays(3)->setHour(11)->toDateTimeString(),
            'status' => 'pending',
        ], ['Accept' => 'application/json']);

        $response->assertStatus(201);
    }
}
