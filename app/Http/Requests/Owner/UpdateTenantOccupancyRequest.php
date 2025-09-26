<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantOccupancyRequest extends FormRequest
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
        $building = $this->route('building');

        return [
            'flat_id' => [
                'required',
                'exists:flats,id',
                Rule::exists('flats', 'id')->where(function ($query) use ($building) {
                    $query->where('building_id', $building->id)
                          ->where('owner_id', auth()->id());
                })
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'flat_id.required' => 'Please select a flat.',
            'flat_id.exists' => 'The selected flat does not exist or does not belong to this building.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after' => 'End date must be after the start date.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateNoOverlappingOccupancy($validator);
        });
    }

    /**
     * Validate that there's no overlapping occupancy for the same flat.
     */
    protected function validateNoOverlappingOccupancy($validator): void
    {
        $flatId = $this->input('flat_id');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $tenant = $this->route('tenant');
        $pivotId = $this->route('pivotId');

        if (!$flatId || !$startDate || !$tenant || !$pivotId) {
            return;
        }

        // Check for overlapping occupancies in the same flat (excluding current record)
        $overlapping = \DB::table('flat_tenant')
            ->where('flat_id', $flatId)
            ->where('id', '!=', $pivotId)
            ->where('tenant_id', '!=', $tenant->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    // Case 1: Existing occupancy starts before our end date (or no end date)
                    $q->where('start_date', '<=', $endDate ?: '9999-12-31');
                })->where(function ($q) use ($startDate) {
                    // Case 2: Existing occupancy ends after our start date (or no end date)
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $startDate);
                });
            })
            ->exists();

        if ($overlapping) {
            $validator->errors()->add('start_date', 'This flat is already occupied during the selected period.');
        }
    }
}