<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreFlatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'flat_number'      => ['required', 'string', 'max:50'],
            'flat_owner_name'  => ['nullable', 'string', 'max:120'],
            'flat_owner_phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'flat_number.required' => 'Flat number is required.',
            'flat_number.string' => 'Flat number must be a valid string.',
            'flat_number.max' => 'Flat number cannot exceed 50 characters.',
            'flat_owner_name.string' => 'Flat owner name must be a valid string.',
            'flat_owner_name.max' => 'Flat owner name cannot exceed 120 characters.',
            'flat_owner_phone.string' => 'Flat owner phone must be a valid string.',
            'flat_owner_phone.max' => 'Flat owner phone cannot exceed 30 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUniqueFlatNumber($validator);
        });
    }

    /**
     * Validate unique flat number within the building.
     */
    protected function validateUniqueFlatNumber($validator): void
    {
        $building = $this->route('building');
        $flatNumber = $this->input('flat_number');

        if (!$building || !$flatNumber) {
            return;
        }

        // Check if flat number already exists in this building
        $exists = DB::table('flats')
            ->where('building_id', $building->id)
            ->where('flat_number', $flatNumber)
            ->exists();

        if ($exists) {
            $validator->errors()->add('flat_number', 'This flat number already exists in this building.');
        }
    }
}