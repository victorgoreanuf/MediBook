<?php

namespace App\Mappers\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\User\User;
use Illuminate\Http\Request;

class AppointmentRequestMapper
{
    /**
     * Map the incoming HTTP request to a strict DTO.
     * This isolates the Controller from knowing how to construct the DTO.
     */
    public static function fromRequest(Request $request): CreateAppointmentDTO
    {
        // 1. Find the internal ID using the public UUID
        $doctorUuid = $request->input('doctor_id');
        $doctor = User::where('doctor_public_id', $doctorUuid)->firstOrFail();

        return new CreateAppointmentDTO(
            doctorId: (int) $doctor->id,
            // We assume the authenticated user is the patient
            patientId: (int) $request->user()->id,
            startTime: $request->input('start_time'),
            endTime: $request->input('end_time')
        );
    }
}
