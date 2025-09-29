<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateOwnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $owner = $this->route('owner');
        return auth()->check() &&
               auth()->user()->role === 'admin' &&
               $owner->role === 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $owner = $this->route('owner');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $owner->id],
            'password' => ['nullable', Rules\Password::defaults()],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Owner name is required.',
            'name.string' => 'Owner name must be a valid string.',
            'name.max' => 'Owner name cannot exceed 255 characters.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'owner name',
            'email' => 'email address',
            'password' => 'password',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $owner = $this->route('owner');

            // Additional validation: ensure we're updating an owner
            if ($owner->role !== 'owner') {
                $validator->errors()->add('owner', 'Invalid owner record.');
            }
        });
    }
}