<?php

namespace App\Queries\Appointment;

use App\Models\Appointment\Appointment;
use Carbon\Carbon;

class GetPatientAvailabilityQuery
{
    /**
     * Check if the patient already has an active appointment with this specific doctor on the given day.
     */
    public function hasBookedDoctorOnDate(int $patientId, int $doctorId, string $date): bool
    {
        return Appointment::query()
            ->where('patient_id', $patientId)
            ->where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    /**
     * Check if the patient has ANY appointment that overlaps with the requested time.
     * (e.g., they can't be at Dr. A and Dr. B at 10:00 AM)
     */
    public function hasTimeConflict(int $patientId, string $startTime, string $endTime): bool
    {
        return Appointment::query()
            ->where('patient_id', $patientId)
            ->where(function ($query) use ($startTime, $endTime) {
                // Overlap logic: (StartA < EndB) and (EndA > StartB)
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->where('status', '!=', 'cancelled')
            ->exists();
    }
}
