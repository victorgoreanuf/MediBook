<?php

namespace App\Queries\Appointment;

use App\Models\Appointment\Appointment;
use Illuminate\Database\Eloquent\Collection;

class GetDoctorScheduleQuery
{
    public function get(int $doctorId): Collection
    {
        return Appointment::query()
            ->with('patient')
            ->where('doctor_id', $doctorId)
            ->where('status', '!=', 'cancelled') // Optional: maybe doctors want to see cancelled ones too?
            ->orderBy('start_time', 'asc')
            ->get();
    }
}
