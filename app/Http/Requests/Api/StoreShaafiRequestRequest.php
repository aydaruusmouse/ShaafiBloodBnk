<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShaafiRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bloodGroups = config('shaafi.blood_groups', []);
        $quantities = config('shaafi.blood_quantities', []);

        return [
            'request_type' => ['required', Rule::in(['donation', 'blood_request'])],
            'full_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'blood_group' => ['required', Rule::in($bloodGroups)],
            'blood_quantity' => [
                Rule::requiredIf(fn () => $this->input('request_type') === 'blood_request'),
                'nullable',
                'integer',
                Rule::in($quantities),
            ],
            'city' => ['required', 'string', 'max:100'],
            'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
            'shaafi_user_id' => ['nullable', 'string', 'max:100'],
            'external_reference' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'blood_quantity.required' => 'Blood quantity is required for blood request submissions.',
            'blood_quantity.in' => 'Blood quantity must be between 1 and 10 bags.',
        ];
    }
}
