<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'string', 'exists:users,doctor_public_id'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
        ];
    }
}
