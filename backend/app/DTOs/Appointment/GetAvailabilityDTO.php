<?php

namespace App\DTOs\Appointment;

final readonly class GetAvailabilityDTO
{
    public function __construct(
        public string $doctorPublicId,
        public string $date
    ) {}

    public static function fromRequest(string $doctorPublicId, array $validatedData): self
    {
        return new self(
            doctorPublicId: $doctorPublicId,
            date: $validatedData['date']
        );
    }
}
