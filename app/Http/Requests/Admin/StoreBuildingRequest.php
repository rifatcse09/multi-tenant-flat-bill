<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'owner_id' => ['required', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'total_floors' => ['nullable', 'integer', 'min:1', 'max:200'],
            'total_units' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Building name is required.',
            'name.max' => 'Building name cannot exceed 255 characters.',
            'address.required' => 'Building address is required.',
            'address.max' => 'Building address cannot exceed 500 characters.',
            'owner_id.required' => 'Please select a building owner.',
            'owner_id.exists' => 'The selected owner does not exist.',
            'total_floors.integer' => 'Total floors must be a number.',
            'total_floors.min' => 'Total floors must be at least 1.',
            'total_floors.max' => 'Total floors cannot exceed 200.',
            'total_units.integer' => 'Total units must be a number.',
            'total_units.min' => 'Total units must be at least 1.',
            'total_units.max' => 'Total units cannot exceed 1000.',
        ];
    }
}