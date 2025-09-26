<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'slug' => ['nullable', 'alpha_dash', 'max:80', 'unique:users,slug'],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Owner name is required.',
            'name.max' => 'Owner name cannot exceed 100 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'slug.alpha_dash' => 'Slug can only contain letters, numbers, dashes and underscores.',
            'slug.unique' => 'This slug is already taken.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }
}