<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Queries\Appointment\GetDoctorScheduleQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DoctorScheduleController extends Controller
{
    public function __construct(
        private readonly GetDoctorScheduleQuery $query
    ) {}

    public function __invoke(Request $request): AnonymousResourceCollection
    {
        // Ensure the user is actually a doctor
        if (!$request->user()->is_doctor) {
            abort(403, 'Only doctors can view this schedule.');
        }

        $appointments = $this->query->get($request->user()->id);

        return AppointmentResource::collection($appointments);
    }
}
