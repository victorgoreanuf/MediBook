<?php

use App\Http\Controllers\Api\V1\Appointment\AvailabilityController;
use App\Http\Controllers\Api\V1\Appointment\DoctorScheduleController;
use App\Http\Controllers\Api\V1\Doctor\DoctorProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\User\DoctorController;
use App\Http\Controllers\Api\V1\Auth\AuthController;

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->group(function () {
    // 🔒 Protect these routes with 'verified'
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Auth Actions
        Route::post('/auth/logout', [AuthController::class, 'logout']); // <--- NEW ROUTE

        // List My Appointments
        Route::get('/appointments', [AppointmentController::class, 'index'])
            ->name('appointments.index');

        // Book Appointment
        Route::post('/appointments', [AppointmentController::class, 'store'])
            ->name('appointments.store');

        // Cancel Appointment
        Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');


        Route::get('/doctors/{id}/availability', AvailabilityController::class);
        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');

        // Doctor
        Route::get('/doctor/schedule', DoctorScheduleController::class);
        Route::put('/doctor/profile', [DoctorProfileController::class, 'update']);
    });

    // Auth routes remain public
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
            ->name('verification.verify');
        // We need a route to resend the verification email
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
            ->middleware(['auth:sanctum', 'throttle:6,1']);
    });
});
