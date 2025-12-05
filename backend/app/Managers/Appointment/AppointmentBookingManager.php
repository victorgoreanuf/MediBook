<?php

namespace App\Managers\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\Appointment\Appointment;
use App\Queries\Appointment\GetDoctorAvailabilityQuery;
use App\Queries\Appointment\GetPatientAvailabilityQuery;
use App\Services\Appointment\AppointmentService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

final readonly class AppointmentBookingManager
{
    public function __construct(
        private AppointmentService $appointmentService,
        private NotificationService $notificationService,
        private GetDoctorAvailabilityQuery $doctorAvailabilityQuery,
        private GetPatientAvailabilityQuery $patientAvailabilityQuery
    ) {}

    /**
     * @throws Exception
     */
    public function book(CreateAppointmentDTO $dto): Appointment
    {
        $isDoctorFree = $this->doctorAvailabilityQuery->isSlotAvailable(
            $dto->doctorId,
            $dto->startTime,
            $dto->endTime
        );

        if (!$isDoctorFree) {
            throw new Exception("Doctor is not available at this time.");
        }

        $date = Carbon::parse($dto->startTime)->toDateString();
        if ($this->patientAvailabilityQuery->hasBookedDoctorOnDate($dto->patientId, $dto->doctorId, $date)) {
            throw new Exception("You already have an appointment with this doctor on this date.");
        }

        if ($this->patientAvailabilityQuery->hasTimeConflict($dto->patientId, $dto->startTime, $dto->endTime)) {
            throw new Exception("You already have another appointment at this time.");
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

    /**
     * @throws Exception
     */
    public function cancel(Appointment $appointment, int $userId): Appointment
    {
        if ($appointment->patient_id !== $userId && $appointment->doctor_id !== $userId) {
            throw new Exception("Unauthorized action.");
        }

        if ($appointment->status === 'cancelled') {
            throw new Exception("Appointment is already cancelled.");
        }

        DB::beginTransaction();
        try {
            $appointment->update(['status' => 'cancelled']);
            DB::commit();
            return $appointment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
