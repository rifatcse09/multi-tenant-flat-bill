<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateFlatRequest extends FormRequest
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
     * Validate unique flat number within the building for updates.
     */
    protected function validateUniqueFlatNumber($validator): void
    {
        $flat = $this->route('flat');
        $flatNumber = $this->input('flat_number');

        if (!$flat || !$flatNumber) {
            return;
        }

        // Check if flat number already exists in this building (excluding current flat)
        $exists = DB::table('flats')
            ->where('building_id', $flat->building_id)
            ->where('flat_number', $flatNumber)
            ->where('id', '!=', $flat->id)
            ->exists();

        if ($exists) {
            $validator->errors()->add('flat_number', 'This flat number already exists in this building.');
        }
    }
}