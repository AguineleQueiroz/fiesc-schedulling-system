<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Models\Availability;
use App\Services\AvailabilityService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availabilityService,
        private readonly UserService $userService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Availability::class);

        return view('availability.index', ['attendants' => $this->userService->all()]);
    }

    public function store(StoreAvailabilityRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($this->availabilityService->hasOverlap(
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

        $availability = $this->availabilityService->create($data);

        return response()->json($availability->load('attendant'), 201);
    }

    public function update(UpdateAvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $data = $request->validated();

        if ($this->availabilityService->hasOverlap(
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

        $updated = $this->availabilityService->update($availability, $data);

        return response()->json($updated);
    }

    public function destroy(Availability $availability): JsonResponse
    {
        $this->authorize('delete', $availability);

        $this->availabilityService->delete($availability);

        return response()->json(null, 204);
    }

    public function byAttendant(int $attendantId): JsonResponse
    {
        return response()->json(
            $this->availabilityService->allForAttendant($attendantId)
        );
    }
}
