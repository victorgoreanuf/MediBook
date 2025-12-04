<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Managers\Appointment\AppointmentBookingManager;
use App\Mappers\Appointment\AppointmentRequestMapper;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class AppointmentController
 * Handles API endpoints for appointment management.
 *
 * Follows strict separation of concerns:
 * - Validation via FormRequest
 * - Data Transformation via Mapper
 * - Business Logic via Manager
 * - Response Formatting via Resource
 */
class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentBookingManager $appointmentManager
    ) {}

    /**
     * Store a new appointment.
     *
     * @param CreateAppointmentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        // 1. Transform the validated request into a strict DTO
        $dto = AppointmentRequestMapper::fromRequest($request);

        // 2. Delegate the complex booking process to the Manager
        // The manager handles transactions, availability checks, and notifications.
        $appointment = $this->appointmentManager->book($dto);

        // 3. Return a standardized JSON response
        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully.',
            'data'    => new AppointmentResource($appointment),
        ], 201);
    }
}
