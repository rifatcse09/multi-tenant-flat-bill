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
            'owner_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'owner_id.required' => 'Please select an owner.',
            'owner_id.exists' => 'The selected owner does not exist.',
            'name.required' => 'Building name is required.',
            'name.string' => 'Building name must be a valid string.',
            'name.max' => 'Building name cannot exceed 120 characters.',
            'address.string' => 'Address must be a valid string.',
            'address.max' => 'Address cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'owner_id' => 'owner',
            'name' => 'building name',
            'address' => 'address',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that the selected user is actually an owner
            if ($this->filled('owner_id')) {
                $user = \App\Models\User::find($this->owner_id);
                if ($user && $user->role !== 'owner') {
                    $validator->errors()->add('owner_id', 'The selected user is not an owner.');
                }
            }
        });
    }
}