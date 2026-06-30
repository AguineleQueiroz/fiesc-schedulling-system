<?php

namespace App\Http\Controllers;

use App\Services\ScheduleService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        private readonly ScheduleService $scheduleService,
        private readonly UserService $userService,
    ) {}

    public function create(): View
    {
        return view('schedule.create', ['attendants' => $this->userService->attendants()]);
    }

    public function slots(Request $request): JsonResponse
    {
        $request->validate([
            'attendant_id' => ['required', 'integer', 'exists:users,id'],
            'date'         => ['required', 'date'],
        ]);

        $attendant = $this->userService->findOrFail($request->integer('attendant_id'));
        $slots     = $this->scheduleService->availableSlots($attendant, $request->string('date'));

        return response()->json($slots);
    }
}
