<?php

namespace App\Mappers\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use Illuminate\Http\Request;

class AppointmentRequestMapper
{
    /**
     * Map the incoming HTTP request to a strict DTO.
     * This isolates the Controller from knowing how to construct the DTO.
     */
    public static function fromRequest(Request $request): CreateAppointmentDTO
    {
        return new CreateAppointmentDTO(
            doctorId: (int) $request->input('doctor_id'),
            // We assume the authenticated user is the patient
            patientId: (int) $request->user()->id,
            startTime: $request->input('start_time'),
            endTime: $request->input('end_time')
        );
    }
}
