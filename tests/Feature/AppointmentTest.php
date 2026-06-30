<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;

function appointmentPayload(int $attendantId, array $overrides = []): array
{
    return array_merge([
        'attendant_id' => $attendantId,
        'client_name'  => 'Cliente Teste',
        'client_phone' => '11999999999',
        'date'         => now()->addDay()->format('Y-m-d'),
        'start_time'   => '09:00',
        'end_time'     => '09:30',
    ], $overrides);
}

test('admin can create appointment for any attendant', function () {
    $admin     = User::factory()->admin()->create();
    $attendant = User::factory()->create();

    $this->actingAs($admin)
        ->postJson('/appointments', appointmentPayload($attendant->id))
        ->assertStatus(201);
});

test('attendant can create appointment for self', function () {
    $attendant = User::factory()->create();

    $this->actingAs($attendant)
        ->postJson('/appointments', appointmentPayload($attendant->id))
        ->assertStatus(201);
});

test('attendant cannot create appointment for another attendant', function () {
    $attendant = User::factory()->create();
    $other     = User::factory()->create();

    $this->actingAs($attendant)
        ->postJson('/appointments', appointmentPayload($other->id))
        ->assertStatus(403);
});

test('admin sees all appointments', function () {
    $admin     = User::factory()->admin()->create();
    $attendant = User::factory()->create();
    $other     = User::factory()->create();

    Appointment::create(appointmentPayload($attendant->id, [
        'client_name' => 'Cliente do Atendente A',
        'status'      => AppointmentStatus::Scheduled,
    ]));
    Appointment::create(appointmentPayload($other->id, [
        'client_name' => 'Cliente do Atendente B',
        'status'      => AppointmentStatus::Scheduled,
    ]));

    $this->actingAs($admin)
        ->get('/appointments')
        ->assertStatus(200)
        ->assertSee('Cliente do Atendente A')
        ->assertSee('Cliente do Atendente B');
});

test('attendant sees only own appointments', function () {
    $attendant = User::factory()->create();
    $other     = User::factory()->create();

    Appointment::create(appointmentPayload($attendant->id, [
        'client_name' => 'Meu Cliente',
        'status'      => AppointmentStatus::Scheduled,
    ]));
    Appointment::create(appointmentPayload($other->id, [
        'client_name' => 'Cliente do Outro',
        'status'      => AppointmentStatus::Scheduled,
    ]));

    $this->actingAs($attendant)
        ->get('/appointments')
        ->assertStatus(200)
        ->assertSee('Meu Cliente')
        ->assertDontSee('Cliente do Outro');
});

test('attendant can cancel own appointment', function () {
    $attendant   = User::factory()->create();
    $appointment = Appointment::create(appointmentPayload($attendant->id, [
        'status' => AppointmentStatus::Scheduled,
    ]));

    $this->actingAs($attendant)
        ->deleteJson("/appointments/{$appointment->id}")
        ->assertStatus(204);

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Cancelled);
});

test('attendant cannot cancel another attendant appointment', function () {
    $attendant   = User::factory()->create();
    $other       = User::factory()->create();
    $appointment = Appointment::create(appointmentPayload($other->id, [
        'status' => AppointmentStatus::Scheduled,
    ]));

    $this->actingAs($attendant)
        ->deleteJson("/appointments/{$appointment->id}")
        ->assertStatus(403);
});
