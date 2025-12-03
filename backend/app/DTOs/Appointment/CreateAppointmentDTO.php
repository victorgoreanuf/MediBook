<?php

namespace App\DTOs\Appointment;

/**
 * This class defines exactly what is needed to book an appointment.
 * If a controller wants to book something, it MUST provide this object.
 */
final class CreateAppointmentDTO
{
    public function __construct(
        public readonly int $doctorId,
        public readonly int $patientId,
        public readonly string $startTime,
        public readonly string $endTime
    ) {}

    /**
     * Helper to create from an array (useful for testing or mapping later)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            doctorId: (int) $data['doctor_id'],
            patientId: (int) $data['patient_id'],
            startTime: $data['start_time'],
            endTime: $data['end_time']
        );
    }
}
