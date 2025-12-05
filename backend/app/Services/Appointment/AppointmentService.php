<?php

namespace App\Services\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\Appointment\Appointment;
use App\Models\User\User;

final class AppointmentService
{
    public function createAppointment(CreateAppointmentDTO $dto): Appointment
    {
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
