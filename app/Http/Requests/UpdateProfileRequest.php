<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->canManageStudio();
    }

    /**
     * @return array<string, list<ValidationRule|string>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $clientId = $user?->client()->value('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => [
                'required',
                'string',
                'max:13',
                'regex:/^\+?\d{11,12}$/',
                Rule::unique('clients', 'phone')->ignore($clientId),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        if (is_string($this->input('name'))) {
            $normalized['name'] = trim(strip_tags($this->input('name')));
        }

        if (is_string($this->input('email'))) {
            $normalized['email'] = mb_strtolower(trim($this->input('email')));
        }

        if (is_string($this->input('phone'))) {
            $phone = trim($this->input('phone'));
            $digits = preg_replace('/\D+/', '', $phone) ?? '';
            $normalized['phone'] = str_starts_with($phone, '+') ? '+'.$digits : $digits;
        }

        $this->merge($normalized);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid Philippine phone number with 11 to 12 digits (e.g. 09171234567 or +639171234567).',
            'phone.unique' => 'This phone number is already connected to another account.',
        ];
    }
}
