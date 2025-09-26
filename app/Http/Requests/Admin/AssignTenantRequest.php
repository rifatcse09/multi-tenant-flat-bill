<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignTenantRequest extends FormRequest
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
            'tenant_id' => ['required', 'exists:tenants,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'tenant_id.required' => 'Please select a tenant.',
            'tenant_id.exists' => 'The selected tenant does not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateTenantNotAlreadyAssigned($validator);
        });
    }

    /**
     * Validate that tenant is not already assigned to building.
     */
    protected function validateTenantNotAlreadyAssigned($validator): void
    {
        $building = $this->route('building');
        $tenantId = $this->input('tenant_id');

        if (!$building || !$tenantId) {
            return;
        }

        // Check if tenant is already assigned to this building
        $alreadyAssigned = $building->tenants()
            ->where('tenant_id', $tenantId)
            ->exists();

        if ($alreadyAssigned) {
            $validator->errors()->add('tenant_id', 'This tenant is already assigned to this building.');
        }
    }
}