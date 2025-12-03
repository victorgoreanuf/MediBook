<?php

namespace App\Services\Notification;

use App\Models\Appointment\Appointment;
use Illuminate\Support\Facades\Log;

final class NotificationService
{
    public function sendConfirmation(Appointment $appointment): void
    {
        // In a real app, this sends an email via Mail::to().
        // For this MVP, we log strictly to MongoDB as per requirements.

        Log::channel('mongodb')->info('Appointment Confirmation Sent', [
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'status' => 'sent',
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
