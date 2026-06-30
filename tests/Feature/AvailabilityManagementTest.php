<?php

use App\Models\Availability;
use App\Models\User;

test('admin can create availability for an attendant', function () {
    $admin     = User::factory()->admin()->create();
    $attendant = User::factory()->create();

    $this->actingAs($admin)
        ->postJson('/availabilities', [
            'user_id'     => $attendant->id,
            'day_of_week' => 1,
            'start_time'  => '08:00',
            'end_time'    => '12:00',
            'active'      => true,
        ])
        ->assertStatus(201);
});

test('attendant cannot create availability', function () {
    $attendant = User::factory()->create();

    $this->actingAs($attendant)
        ->postJson('/availabilities', [
            'user_id'     => $attendant->id,
            'day_of_week' => 1,
            'start_time'  => '08:00',
            'end_time'    => '12:00',
            'active'      => true,
        ])
        ->assertStatus(403);
});

test('overlapping availability is rejected with 422', function () {
    $admin     = User::factory()->admin()->create();
    $attendant = User::factory()->create();

    Availability::create([
        'user_id'     => $attendant->id,
        'day_of_week' => 1,
        'start_time'  => '08:00',
        'end_time'    => '12:00',
        'active'      => true,
    ]);

    $this->actingAs($admin)
        ->postJson('/availabilities', [
            'user_id'     => $attendant->id,
            'day_of_week' => 1,
            'start_time'  => '10:00',
            'end_time'    => '14:00',
            'active'      => true,
        ])
        ->assertStatus(422);
});
