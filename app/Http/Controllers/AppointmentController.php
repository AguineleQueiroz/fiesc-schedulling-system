<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentRepository $repository)
    {
    }

    public function index(): View
    {
        $appointments = $this->repository->listForUser(auth()->user());

        return view('appointments.index', compact('appointments'));
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->repository->create($request->validated());

        return response()->json($appointment->load('attendant'), 201);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $user = auth()->user();

        if ($user->isAttendant() && $user->id !== $appointment->attendant_id) {
            abort(403);
        }

        $this->repository->cancel($appointment);
        return response()->json(null, 204);
    }
}
