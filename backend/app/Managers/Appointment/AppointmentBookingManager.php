<?php

namespace App\Managers\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\Appointment\Appointment;
use App\Queries\Appointment\GetDoctorAvailabilityQuery;
use App\Services\Appointment\AppointmentService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Exception;

final class AppointmentBookingManager
{
    // Inject all necessary workers
    public function __construct(
        private readonly AppointmentService $appointmentService,
        private readonly NotificationService $notificationService,
        private readonly GetDoctorAvailabilityQuery $availabilityQuery
    ) {}

    /**
     * Coordinate the entire booking process.
     * @throws Exception
     */
    public function book(CreateAppointmentDTO $dto): Appointment
    {
        // 1. Validation Logic (via Query)
        $isAvailable = $this->availabilityQuery->isSlotAvailable(
            $dto->doctorId,
            $dto->startTime,
            $dto->endTime
        );

        if (!$isAvailable) {
            throw new Exception("Doctor is not available at this time.");
        }

        // 2. Transactional Logic (via Services)
        // The Bible explicitly uses DB::beginTransaction() in Managers [cite: 272]
        DB::beginTransaction();

        try {
            // A. Create the appointment
            $appointment = $this->appointmentService->createAppointment($dto);

            // B. Send Notification (Log to MongoDB)
            $this->notificationService->sendConfirmation($appointment);

            // C. Commit changes
            DB::commit();

            return $appointment;

        } catch (Exception $e) {
            // D. Rollback if ANYTHING fails
            DB::rollBack();
            throw $e;
        }
    }
}
