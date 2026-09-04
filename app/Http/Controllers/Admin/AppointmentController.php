<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = $this->bookingService->getAppointmentsForCompany(
            auth()->user()->company_id,
            $request->only(['status', 'customer_id', 'date', 'per_page'])
        );

        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        $teamMembers = User::where('company_id', auth()->user()->company_id)->get();

        if (view()->exists('admin.appointments.index')) {
            return view('admin.appointments.index', compact('appointments', 'customers', 'teamMembers'));
        }

        return response()->json($appointments);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        $appointment = $this->bookingService->createAppointment(
            $request->validated(),
            auth()->user()->company_id,
            auth()->user()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Appointment scheduled successfully.', 'appointment' => $appointment], 201);
        }

        toastr()->success('Appointment scheduled successfully.');
        return redirect()->back();
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        $appointment->load(['customer', 'user']);

        if (request()->wantsJson()) {
            return response()->json($appointment);
        }

        return view('admin.appointments.show', compact('appointment'));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $updated = $this->bookingService->updateAppointment($appointment, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Appointment updated successfully.', 'appointment' => $updated]);
        }

        toastr()->success('Appointment updated successfully.');
        return redirect()->back();
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        $this->bookingService->cancelAppointment($appointment, 'Deleted by administrator');

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Appointment cancelled successfully.']);
        }

        toastr()->success('Appointment cancelled.');
        return redirect()->back();
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $slots = $this->bookingService->getAvailableSlots(auth()->user()->company_id, $request->date);

        return response()->json($slots);
    }
}