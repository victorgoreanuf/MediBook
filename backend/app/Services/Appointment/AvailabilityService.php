<?php

namespace App\Services\Appointment;

use App\DTOs\Appointment\GetAvailabilityDTO;
use App\Models\User\User;
use App\Queries\Appointment\GetDoctorAvailabilityQuery;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class AvailabilityService
{
    public function __construct(
        private GetDoctorAvailabilityQuery $query
    ) {}

    /**
     * @return array<int, array{time: string, is_booked: bool}>
     * @throws ModelNotFoundException
     */
    public function getDailySlots(GetAvailabilityDTO $dto): array
    {
        $doctor = User::where('doctor_public_id', $dto->doctorPublicId)->firstOrFail();

        return $this->query->getDailySlots(
            $doctor->id,
            $dto->date
        );
    }
}
