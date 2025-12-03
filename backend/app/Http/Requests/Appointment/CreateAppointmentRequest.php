<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // In a real app, you might check permissions here.
        // For now, we allow any authenticated user to try.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Ensure the doctor exists in the users table
            'doctor_id' => ['required', 'integer', 'exists:users,id'],

            // Validate strict date format and ensure it's in the future
            'start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],

            // End time must be after start time
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
        ];
    }
}
