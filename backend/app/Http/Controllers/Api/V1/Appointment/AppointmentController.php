<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Managers\Appointment\AppointmentBookingManager;
use App\Mappers\Appointment\AppointmentRequestMapper;
use Illuminate\Http\JsonResponse;
use Exception;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentBookingManager $appointmentManager
    ) {}

    /**
     * @param CreateAppointmentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        $dto = AppointmentRequestMapper::fromRequest($request);

        try {
            $appointment = $this->appointmentManager->book($dto);

            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully.',
                'data'    => new AppointmentResource($appointment),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'booking_conflict'
            ], 409);
        }
    }
}
