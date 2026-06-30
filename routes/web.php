<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('users.index'));

    // Módulo de Usuários
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Módulo de Disponibilidade
    Route::get('/availabilities', [AvailabilityController::class, 'index'])->name('availabilities.index');
    Route::get('/availabilities/attendant/{id}', [AvailabilityController::class, 'byAttendant'])->name('availabilities.by-attendant');
    Route::post('/availabilities', [AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::put('/availabilities/{availability}', [AvailabilityController::class, 'update'])->name('availabilities.update');
    Route::delete('/availabilities/{availability}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');

    // Módulo de Agendamentos
    Route::get('/schedule', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/schedule/create', [ScheduleController::class, 'create'])->name('schedule.create');
    Route::get('/schedule/slots', [ScheduleController::class, 'slots'])->name('schedule.slots');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Perfil (troca de senha)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__ . '/auth.php';
