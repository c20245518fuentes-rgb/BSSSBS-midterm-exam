<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<ValidationRule|string>|string>
     */
    public function rules(): array
    {
        $isInitialSetup = User::query()->doesntExist();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => $isInitialSetup
                ? ['nullable', 'string', 'max:30']
                : ['required', 'string', 'max:13', 'regex:/^\+?\d{11,12}$/', 'unique:clients,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
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
            'phone.unique' => 'This phone number is already connected to a client account.',
        ];
    }
}
