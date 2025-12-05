<?php

namespace App\Http\Resources\Appointment;

use App\Models\Appointment\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Appointment
 */
class AppointmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time' => $this->end_time->toIso8601String(),

            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->name,
                'specialization' => $this->doctor->specialization,
            ],

            'patient' => $this->when($request->user() && $request->user()->is_doctor, function () {
                return [
                    'id' => $this->patient->id,
                    'name' => $this->patient->name,
                    'email' => $this->patient->email, // Doctors need email to contact patients
                ];
            }),
            // We exclude 'created_at', 'updated_at', and 'price' if not needed for this view
        ];
    }
}
