<?php

namespace App\Managers\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\Appointment\Appointment;
use App\Queries\Appointment\GetDoctorAvailabilityQuery;
use App\Services\Appointment\AppointmentService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Exception;

final readonly class AppointmentBookingManager
{
    public function __construct(
        private AppointmentService $appointmentService,
        private NotificationService $notificationService,
        private GetDoctorAvailabilityQuery $availabilityQuery
    ) {}

    /**
     * @throws Exception
     */
    public function book(CreateAppointmentDTO $dto): Appointment
    {
        $isAvailable = $this->availabilityQuery->isSlotAvailable(
            $dto->doctorId,
            $dto->startTime,
            $dto->endTime
        );

        if (!$isAvailable) {
            throw new Exception("Doctor is not available at this time.");
        }

        DB::beginTransaction();

        try {
            $appointment = $this->appointmentService->createAppointment($dto);

            $this->notificationService->sendConfirmation($appointment);

            DB::commit();

            return $appointment;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
