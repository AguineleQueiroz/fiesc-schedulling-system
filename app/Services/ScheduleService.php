<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\Carbon;

class ScheduleService
{
    public function availableSlots(User $attendant, string $date): array
    {
        $dayOfWeek = (int)Carbon::parse($date)->dayOfWeek; // 0=domingo … 6=sábado

        $availabilities = Availability::where('user_id', $attendant->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('active', true)
            ->get();

        $existingAppointments = Appointment::where('attendant_id', $attendant->id)
            ->where('date', $date)
            ->where('status', AppointmentStatus::Scheduled)
            ->get();

        $slotMinutes = (int)config('scheduling.slot_duration_minutes', 30);
        $slots = [];

        foreach ($availabilities as $availability) {
            $current = Carbon::parse($date . ' ' . $availability->start_time);
            $windowEnd = Carbon::parse($date . ' ' . $availability->end_time);

            while ($current->copy()->addMinutes($slotMinutes)->lte($windowEnd)) {
                $slotEnd = $current->copy()->addMinutes($slotMinutes);

                $occupied = $existingAppointments->contains(function ($appt) use ($current, $slotEnd) {
                    $apptStart = Carbon::parse($appt->start_time);
                    $apptEnd = Carbon::parse($appt->end_time);
                    return $current->lt($apptEnd) && $slotEnd->gt($apptStart);
                });

                if (!$occupied) {
                    $slots[] = [
                        'start' => $current->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'label' => $current->format('H:i') . ' – ' . $slotEnd->format('H:i'),
                    ];
                }

                $current->addMinutes($slotMinutes);
            }
        }

        usort($slots, fn($a, $b) => strcmp($a['start'], $b['start']));

        return $slots;
    }
}
