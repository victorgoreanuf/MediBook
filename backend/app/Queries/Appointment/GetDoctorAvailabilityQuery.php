<?php

namespace App\Queries\Appointment;

use App\Models\Appointment\Appointment;
use App\Models\User\User;
use Carbon\Carbon;

class GetDoctorAvailabilityQuery
{
    /**
     * Check if a specific slot is valid and free.
     */
    public function isSlotAvailable(int $doctorId, string $startTime, string $endTime): bool
    {
        // 1. Check Intersection with existing appointments
        $overlaps = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($overlaps) {
            return false;
        }

        // 2. Validate against Doctor's Schedule
        $doctor = User::find($doctorId);
        $startCarbon = Carbon::parse($startTime);
        $requestedTime = $startCarbon->format('H:i'); // "09:00"

        // If doctor has specific hours, the requested time MUST be one of them
        if ($doctor->available_hours && !in_array($requestedTime, $doctor->available_hours)) {
            return false;
        }

        return true;
    }

    /**
     * Get the full list of slots for a specific day with their status.
     * Use this for the Frontend UI.
     * * @return array [ ['time' => '09:00', 'is_booked' => false], ... ]
     */
    public function getDailySlots(int $doctorId, string $date): array
    {
        $doctor = User::findOrFail($doctorId);
        $baseSlots = $doctor->available_hours ?? []; // e.g. ["09:00", "10:00"]

        // Get all booked start times for this day
        $bookedTimes = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('start_time') // Returns collection of Carbon objects or strings
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        // Build the result array
        $results = [];
        foreach ($baseSlots as $time) {
            $results[] = [
                'time' => $time,
                'is_booked' => in_array($time, $bookedTimes)
            ];
        }

        return $results;
    }
}
