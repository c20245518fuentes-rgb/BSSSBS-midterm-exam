<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'appointment_datetime' => ['required', 'date'],
            'status' => ['sometimes', 'in:'.Appointment::STATUS_SCHEDULED.','.Appointment::STATUS_CANCELLED],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.exists' => 'Select an existing client.',
            'stylist_id.exists' => 'Select an existing stylist.',
            'service_id.exists' => 'Select an existing service.',
            'status.in' => 'Appointments can only be scheduled or cancelled here; payment completes them.',
        ];
    }
}
