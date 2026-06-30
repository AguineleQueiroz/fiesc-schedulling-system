<?php

namespace App\Services;

use App\Models\Availability;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityService
{
    public function allForAttendant(int $attendantId): Collection
    {
        return Availability::where('user_id', $attendantId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function create(array $data): Availability
    {
        return Availability::create($data);
    }

    public function update(Availability $availability, array $data): Availability
    {
        $availability->update($data);

        return $availability->fresh();
    }

    public function delete(Availability $availability): void
    {
        $availability->delete();
    }

    public function hasOverlap(
        int $attendantId,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = Availability::where('user_id', $attendantId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
