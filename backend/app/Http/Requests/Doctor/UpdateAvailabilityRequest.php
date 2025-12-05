<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->is_doctor;
    }

    public function rules(): array
    {
        return [
            'available_hours' => ['required', 'array'],
            'available_hours.*' => ['string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'], // Validate "09:00" format
            'bio' => ['nullable', 'string', 'max:1000'],
            'specialization' => ['nullable', 'string', 'max:255'],
        ];
    }
}
