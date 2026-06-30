<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use App\Services\AppointmentService;

test('available slots are generated from availability window', function () {
    $attendant = User::factory()->create();
    $date      = '2025-01-06'; // Segunda-feira (day_of_week = 1)

    Availability::create([
        'user_id'     => $attendant->id,
        'day_of_week' => 1,
        'start_time'  => '08:00:00',
        'end_time'    => '09:00:00',
        'active'      => true,
    ]);

    $slots = app(AppointmentService::class)->availableSlots($attendant, $date);

    expect($slots)->toHaveCount(2)
        ->and($slots[0]['start'])->toBe('08:00')
        ->and($slots[0]['end'])->toBe('08:30')
        ->and($slots[1]['start'])->toBe('08:30')
        ->and($slots[1]['end'])->toBe('09:00');
});

test('occupied slots are excluded from available slots', function () {
    $attendant = User::factory()->create();
    $date      = '2025-01-06'; // Segunda-feira

    Availability::create([
        'user_id'     => $attendant->id,
        'day_of_week' => 1,
        'start_time'  => '08:00:00',
        'end_time'    => '09:00:00',
        'active'      => true,
    ]);

    Appointment::create([
        'attendant_id' => $attendant->id,
        'client_name'  => 'Cliente',
        'client_phone' => '11999999999',
        'date'         => $date,
        'start_time'   => '08:00:00',
        'end_time'     => '08:30:00',
        'status'       => AppointmentStatus::Scheduled,
    ]);

    $slots = app(AppointmentService::class)->availableSlots($attendant, $date);

    expect($slots)->toHaveCount(1)
        ->and($slots[0]['start'])->toBe('08:30')
        ->and($slots[0]['end'])->toBe('09:00');
});

test('inactive availability windows do not generate slots', function () {
    $attendant = User::factory()->create();
    $date      = '2025-01-06'; // Segunda-feira

    Availability::create([
        'user_id'     => $attendant->id,
        'day_of_week' => 1,
        'start_time'  => '08:00:00',
        'end_time'    => '09:00:00',
        'active'      => false,
    ]);

    $slots = app(AppointmentService::class)->availableSlots($attendant, $date);

    expect($slots)->toBeEmpty();
});

test('cancelled appointment does not block slot', function () {
    $attendant = User::factory()->create();
    $date      = '2025-01-06'; // Segunda-feira

    Availability::create([
        'user_id'     => $attendant->id,
        'day_of_week' => 1,
        'start_time'  => '08:00:00',
        'end_time'    => '09:00:00',
        'active'      => true,
    ]);

    Appointment::create([
        'attendant_id' => $attendant->id,
        'client_name'  => 'Cliente',
        'client_phone' => '11999999999',
        'date'         => $date,
        'start_time'   => '08:00:00',
        'end_time'     => '08:30:00',
        'status'       => AppointmentStatus::Cancelled,
    ]);

    $slots = app(AppointmentService::class)->availableSlots($attendant, $date);

    expect($slots)->toHaveCount(2);
});
