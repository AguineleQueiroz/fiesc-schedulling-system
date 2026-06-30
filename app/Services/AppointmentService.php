<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

readonly class AppointmentService
{
    public function __construct(private AppointmentRepository $repository) {}

    public function list(User $user): Collection
    {
        return $this->repository->listForUser($user);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create(array_merge($data, ['status' => AppointmentStatus::Scheduled]));
    }

    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        return $appointment->fresh();
    }

    public function availableSlots(User $attendant, string $date): array
    {
        $dayOfWeek = (int) Carbon::parse($date)->dayOfWeek;

        $availabilities = Availability::where('user_id', $attendant->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('active', true)
            ->get();

        $existingAppointments = $this->repository->scheduledForAttendantOnDate($attendant->id, $date);

        $slotMinutes = (int) config('scheduling.slot_duration_minutes', 30);
        $slots       = [];

        foreach ($availabilities as $availability) {
            $current   = Carbon::parse($date . ' ' . $availability->start_time);
            $windowEnd = Carbon::parse($date . ' ' . $availability->end_time);

            while ($current->copy()->addMinutes($slotMinutes)->lte($windowEnd)) {
                $slotEnd = $current->copy()->addMinutes($slotMinutes);

                $occupied = $existingAppointments->contains(function ($appt) use ($current, $slotEnd, $date) {
                    $apptStart = Carbon::parse($date . ' ' . $appt->start_time);
                    $apptEnd   = Carbon::parse($date . ' ' . $appt->end_time);

                    return $current->lt($apptEnd) && $slotEnd->gt($apptStart);
                });

                if (!$occupied) {
                    $slots[] = [
                        'start' => $current->format('H:i'),
                        'end'   => $slotEnd->format('H:i'),
                        'label' => $current->format('H:i') . ' – ' . $slotEnd->format('H:i'),
                    ];
                }

                $current->addMinutes($slotMinutes);
            }
        }

        usort($slots, fn ($a, $b) => strcmp($a['start'], $b['start']));

        return $slots;
    }
}
