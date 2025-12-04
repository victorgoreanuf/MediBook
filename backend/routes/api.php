<?php

use App\Http\Controllers\Api\V1\Appointment\AvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\User\DoctorController;
use App\Http\Controllers\Api\V1\Auth\AuthController;

Route::get('/test-connection', function () {
    return response()->json(['message' => 'BACKEND API IS WORKING']);
});

// Public Auth Routes
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('v1')->group(function () {
    // Protected Routes (Require Token)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth Actions
        Route::post('/auth/logout', [AuthController::class, 'logout']); // <--- NEW ROUTE

        // Domain Actions
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/doctors/{id}/availability', AvailabilityController::class);
    });

    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
