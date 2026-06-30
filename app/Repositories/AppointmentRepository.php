<?php

namespace App\Repositories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository
{
    public function listForUser(User $user): Collection
    {
        $query = Appointment::with('attendant')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($user->isAttendant()) {
            $query->where('attendant_id', $user->id);
        }

        return $query->get();
    }

    public function scheduledForAttendantOnDate(int $attendantId, string $date): Collection
    {
        return Appointment::where('attendant_id', $attendantId)
            ->where('date', $date)
            ->where('status', AppointmentStatus::Scheduled)
            ->get();
    }
}
