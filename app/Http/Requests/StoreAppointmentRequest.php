<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
        $canManageStudio = $this->user()->canManageStudio();

        return [
            'client_id' => $canManageStudio
                ? ['required', 'integer', 'exists:clients,id']
                : ['prohibited'],
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'appointment_datetime' => ['required', 'date'],
            'status' => $canManageStudio
                ? ['sometimes', 'in:'.Appointment::STATUS_SCHEDULED.','.Appointment::STATUS_CANCELLED]
                : ['prohibited'],
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
