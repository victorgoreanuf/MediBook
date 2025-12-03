<?php

namespace App\Services\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\Appointment\Appointment;
use App\Models\User\User;

final class AppointmentService
{
    /**
     * Create the appointment record in the database.
     * Use strictly typed DTOs as per strict guidelines.
     */
    public function createAppointment(CreateAppointmentDTO $dto): Appointment
    {
        // Business Logic: Calculate price (simplified for MVP)
        // In a real app, you might inject a PriceService here
        $doctor = User::find($dto->doctorId);
        $hourlyRate = 100.00; // Hardcoded or fetched from doctor profile

        return Appointment::create([
            'doctor_id' => $dto->doctorId,
            'patient_id' => $dto->patientId,
            'start_time' => $dto->startTime,
            'end_time' => $dto->endTime,
            'price' => $hourlyRate,
            'status' => 'scheduled'
        ]);
    }
}
