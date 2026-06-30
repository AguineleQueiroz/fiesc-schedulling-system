<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Illuminate\Database\Eloquent\Collection;

class AppointmentService
{
    public function __construct(private readonly AppointmentRepository $repository) {}

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
}
