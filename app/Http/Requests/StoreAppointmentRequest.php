<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->is_admin;
    }

    /**
     * @return array<string, list<ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'appointment_datetime' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'stylist_id.exists' => 'Select an existing stylist.',
            'service_id.exists' => 'Select an existing service.',
        ];
    }
}
