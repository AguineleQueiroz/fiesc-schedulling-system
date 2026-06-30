<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Models\Availability;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(private readonly AvailabilityService $service)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Availability::class);

        $attendants = User::orderBy('name')->get();

        return view('availability.index', compact('attendants'));
    }

    public function store(StoreAvailabilityRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($this->service->hasOverlap(
            $data['user_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time']
        )) {
            return response()->json(
                ['message' => 'Este horário conflita com uma disponibilidade já cadastrada.'],
                422
            );
        }

        $availability = Availability::create($data);

        return response()->json($availability->load('attendant'), 201);
    }

    public function update(UpdateAvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $data = $request->validated();

        if ($this->service->hasOverlap(
            $availability->user_id,
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $availability->id
        )) {
            return response()->json(
                ['message' => 'Este horário conflita com uma disponibilidade já cadastrada.'],
                422
            );
        }

        $availability->update($data);

        return response()->json($availability->fresh());
    }

    public function destroy(Availability $availability): JsonResponse
    {
        $this->authorize('delete', $availability);

        $availability->delete();

        return response()->json(null, 204);
    }

    public function byAttendant(int $attendantId): JsonResponse
    {
        $availabilities = Availability::where('user_id', $attendantId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($availabilities);
    }
}
