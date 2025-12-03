<?php

namespace App\Queries\Appointment;

use App\Models\Appointment\Appointment;
use Illuminate\Database\Eloquent\Collection;

final class GetDoctorAvailabilityQuery
{
    /**
     * Check if a doctor has any appointments overlapping with the requested time.
     * * @param int $doctorId
     * @param string $startTime
     * @param string $endTime
     * @return bool True if the slot is free (no appointments found).
     */
    public function isSlotAvailable(int $doctorId, string $startTime, string $endTime): bool
    {
        // strictly typed logic as per [cite: 36]
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where(function ($query) use ($startTime, $endTime) {
                // Logic: An appointment overlaps if it starts before the requested end
                // AND ends after the requested start.
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            // We only care about active appointments, not cancelled ones
            ->where('status', '!=', 'cancelled')
            ->doesntExist(); // Returns true if NO matching records exist
    }

    /**
     * Get all appointments for a doctor on a specific day (for the calendar view).
     */
    public function getDailySchedule(int $doctorId, string $date): Collection
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->orderBy('start_time')
            ->get();
    }
}
