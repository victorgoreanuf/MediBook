<?php

namespace App\Queries\Appointment;

use App\Models\Appointment\Appointment;
use App\Models\User\User;
use Carbon\Carbon;

class GetDoctorAvailabilityQuery
{
    public function isSlotAvailable(int $doctorId, string $startTime, string $endTime): bool
    {
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

        $doctor = User::find($doctorId);
        $startCarbon = Carbon::parse($startTime);
        $requestedTime = $startCarbon->format('H:i');

        if ($doctor->available_hours && !in_array($requestedTime, $doctor->available_hours)) {
            return false;
        }

        return true;
    }

    /**
     * * @return array [ ['time' => '09:00', 'is_booked' => false], ... ]
     */
    public function getDailySlots(int $doctorId, string $date): array
    {
        $doctor = User::findOrFail($doctorId);
        $baseSlots = $doctor->available_hours ?? [];

        // Get all booked start times for this day
        $bookedTimes = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('start_time') // Returns collection of Carbon objects or strings
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

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
