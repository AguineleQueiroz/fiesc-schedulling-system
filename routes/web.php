<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('users.index'));

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/availabilities', [AvailabilityController::class, 'index'])->name('availabilities.index');
    Route::get('/availabilities/attendant/{id}', [AvailabilityController::class, 'byAttendant'])->name('availabilities.by-attendant');
    Route::post('/availabilities', [AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::put('/availabilities/{availability}', [AvailabilityController::class, 'update'])->name('availabilities.update');
    Route::delete('/availabilities/{availability}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');

    Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__ . '/auth.php';
