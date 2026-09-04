<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $appointments = $this->bookingService->getAppointmentsForCompany(
            $request->user()?->company_id,
            $request->only(['status', 'customer_id', 'date', 'per_page'])
        );

        return response()->json($appointments);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->bookingService->createAppointment(
            $request->validated(),
            $request->user()?->company_id,
            $request->user()
        );

        return response()->json($appointment, 201);
    }

    public function show(Appointment $appointment)
    {
        return response()->json($appointment->load(['customer', 'user']));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $updated = $this->bookingService->updateAppointment($appointment, $request->validated());

        return response()->json($updated);
    }

    public function destroy(Appointment $appointment)
    {
        $this->bookingService->cancelAppointment($appointment, 'Cancelled via API');

        return response()->json(['message' => 'Appointment cancelled.']);
    }
}
