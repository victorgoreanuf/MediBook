<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Managers\Appointment\AppointmentBookingManager;
use App\Mappers\Appointment\AppointmentRequestMapper;
use App\Queries\Appointment\GetPatientAppointmentsQuery;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\Appointment\Appointment;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentBookingManager $appointmentManager,
        private readonly GetPatientAppointmentsQuery $appointmentsQuery,
    ) {}

    /**
     * List my appointments.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $patientId = $request->user()->id;
        $appointments = $this->appointmentsQuery->get($patientId);

        return AppointmentResource::collection($appointments);
    }


    /**
     * Cancel an appointment.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        try {
            $this->appointmentManager->cancel($appointment, $request->user()->id);

            return response()->json([
                'message' => 'Appointment cancelled successfully.',
                'data' => new AppointmentResource($appointment->refresh())
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

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
