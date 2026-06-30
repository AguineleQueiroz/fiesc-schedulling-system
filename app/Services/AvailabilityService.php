<?php

namespace App\Services;

use App\Models\Availability;

class AvailabilityService
{
    public function hasOverlap(
        int    $attendantId,
        int    $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int   $excludeId = null
    ): bool
    {
        $query = Availability::where('user_id', $attendantId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($query) use ($startTime, $endTime) {
                /**
                 * Qualquer janela que se sobreponha ao intervalo informado
                 */
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
