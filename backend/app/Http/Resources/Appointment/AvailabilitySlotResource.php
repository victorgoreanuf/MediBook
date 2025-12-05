<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilitySlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'time' => $this->resource['time'],
            'is_booked' => (bool) $this->resource['is_booked'],
            'status' => $this->resource['is_booked'] ? 'booked' : 'available'
        ];
    }
}
