<?php

use App\Models\User;

test('admin can create a user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->postJson('/users', [
            'name'                  => 'Novo Usuário',
            'email'                 => 'novo@example.com',
            'role'                  => 'atendente',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertStatus(201);

    $this->assertDatabaseHas('users', ['email' => 'novo@example.com']);
});

test('attendant cannot create a user', function () {
    $attendant = User::factory()->create();

    $this->actingAs($attendant)
        ->postJson('/users', [
            'name'                  => 'Outro',
            'email'                 => 'outro@example.com',
            'role'                  => 'atendente',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertStatus(403);
});

test('attendant can edit own profile', function () {
    $attendant = User::factory()->create();

    $this->actingAs($attendant)
        ->putJson("/users/{$attendant->id}", ['name' => 'Nome Atualizado'])
        ->assertStatus(200);

    $this->assertDatabaseHas('users', ['id' => $attendant->id, 'name' => 'Nome Atualizado']);
});

test('attendant cannot edit another user', function () {
    $attendant = User::factory()->create();
    $other     = User::factory()->create();

    $this->actingAs($attendant)
        ->putJson("/users/{$other->id}", ['name' => 'Hacked'])
        ->assertStatus(403);
});

test('admin can delete another user', function () {
    $admin = User::factory()->admin()->create();
    $user  = User::factory()->create();

    $this->actingAs($admin)
        ->deleteJson("/users/{$user->id}")
        ->assertStatus(204);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('admin cannot delete self', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->deleteJson("/users/{$admin->id}")
        ->assertStatus(403);
});

test('attendant cannot delete users', function () {
    $attendant = User::factory()->create();
    $other     = User::factory()->create();

    $this->actingAs($attendant)
        ->deleteJson("/users/{$other->id}")
        ->assertStatus(403);
});
