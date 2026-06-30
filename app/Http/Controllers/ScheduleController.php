<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(private readonly ScheduleService $service)
    {
    }

    public function create(): View
    {
        $attendants = User::where('role', 'atendente')->orderBy('name')->get();

        return view('schedule.create', compact('attendants'));
    }

    public function slots(Request $request): JsonResponse
    {
        $request->validate([
            'attendant_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
        ]);

        $attendant = User::findOrFail($request->integer('attendant_id'));
        $slots = $this->service->availableSlots($attendant, $request->string('date'));

        return response()->json($slots);
    }
}
