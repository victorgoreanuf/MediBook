<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AvailabilityDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
