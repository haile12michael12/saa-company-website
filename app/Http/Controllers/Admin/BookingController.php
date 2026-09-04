<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        return app(AppointmentController::class)->index($request);
    }

    public function store(StoreAppointmentRequest $request)
    {
        return app(AppointmentController::class)->store($request);
    }

    public function slots(Request $request)
    {
        return app(AppointmentController::class)->getAvailableSlots($request);
    }
}