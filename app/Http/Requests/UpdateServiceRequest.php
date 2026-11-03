<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['category', 'name'] as $field) {
            $value = $this->input($field);
            if (is_string($value)) {
                $normalized[$field] = Str::title(trim(strip_tags($value)));
            }
        }

        $this->merge($normalized);
    }

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
            'category' => ['required', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('services', 'name')->ignore($this->route('service')),
            ],
            'duration_min' => ['required', 'integer', 'min:1', 'max:1440'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A service with this title already exists.',
            'duration_min.min' => 'The service duration must be at least one minute.',
            'price.min' => 'The service price cannot be negative.',
        ];
    }
}
