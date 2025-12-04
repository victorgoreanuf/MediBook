<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Queries\Appointment\GetDoctorAvailabilityQuery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly GetDoctorAvailabilityQuery $query
    ) {}

    public function __invoke(Request $request, string $doctorPublicId): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        // Resolve UUID to ID
        $doctor = User::where('doctor_public_id', $doctorPublicId)->firstOrFail();

        $slots = $this->query->getDailySlots(
            $doctor->id,
            $request->input('date')
        );

        return response()->json([
            'data' => $slots
        ]);
    }
}
