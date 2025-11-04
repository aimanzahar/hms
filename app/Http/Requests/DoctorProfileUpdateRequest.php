<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDoctor() ?? false;
    }

    public function rules(): array
    {
        $doctorId = $this->user()?->doctor?->id;

        return [
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('doctors', 'license_number')->ignore($doctorId),
            ],
            'experience_years' => ['required', 'integer', 'min:0', 'max:100'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
        ];
    }
}