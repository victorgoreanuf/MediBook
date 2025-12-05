<?php

namespace App\Queries\Appointment;

use App\Models\Appointment\Appointment;
use Illuminate\Database\Eloquent\Collection;

class GetPatientAppointmentsQuery
{
    /**
     * Get all appointments for a specific patient.
     *
     * @param int $patientId
     * @return Collection
     */
    public function get(int $patientId): Collection
    {
        return Appointment::query()
            ->with('doctor') // Eager load doctor details
            ->where('patient_id', $patientId)
            ->orderBy('start_time', 'asc') // Show upcoming first
            ->get();
    }
}
