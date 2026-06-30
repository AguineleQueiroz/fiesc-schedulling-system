<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $service,
        private readonly UserService $userService,
    ) {}

    public function index(): View
    {
        return view('appointments.index', [
            'appointments' => $this->service->list(auth()->user()),
        ]);
    }

    public function create(): View
    {
        return view('appointments.create', [
            'attendants' => $this->userService->attendants(),
        ]);
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'attendant_id' => ['required', 'integer', 'exists:users,id'],
            'date'         => ['required', 'date'],
        ]);

        $attendant = $this->userService->findOrFail($request->integer('attendant_id'));

        return response()->json(
            $this->service->availableSlots($attendant, $request->string('date'))
        );
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
