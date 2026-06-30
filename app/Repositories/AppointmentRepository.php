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

        if ($user->isAtendente()) {
            $query->where('attendant_id', $user->id);
        }

        return $query->get();
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
}
