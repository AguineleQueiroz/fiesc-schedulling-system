<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $service) {}

    public function index(): View
    {
        return view('appointments.index', [
            'appointments' => $this->service->list(auth()->user()),
        ]);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->service->create($request->validated());

        return response()->json($appointment->load('attendant'), 201);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        if (auth()->user()->isAttendant() && auth()->id() !== $appointment->attendant_id) {
            abort(403);
        }

        $this->service->cancel($appointment);

        return response()->json(null, 204);
    }
}
