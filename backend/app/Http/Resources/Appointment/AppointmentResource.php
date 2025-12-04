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
     * Transform the resource into an array.
     * Use this to hide internal DB columns and format dates standardly.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time' => $this->end_time->toIso8601String(),
            // Nested relationship data
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->name,
                'specialization' => $this->doctor->specialization,
            ],
            // We exclude 'created_at', 'updated_at', and 'price' if not needed for this view
        ];
    }
}
