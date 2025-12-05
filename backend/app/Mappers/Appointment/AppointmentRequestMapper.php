<?php

namespace App\Mappers\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\User\User;
use Illuminate\Http\Request;

class AppointmentRequestMapper
{
    public static function fromRequest(Request $request): CreateAppointmentDTO
    {
        $doctorUuid = $request->input('doctor_id');
        $doctor = User::where('doctor_public_id', $doctorUuid)->firstOrFail();

        return new CreateAppointmentDTO(
            doctorId: (int) $doctor->id,
            patientId: (int) $request->user()->id,
            startTime: $request->input('start_time'),
            endTime: $request->input('end_time')
        );
    }
}
