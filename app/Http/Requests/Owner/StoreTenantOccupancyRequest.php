<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreTenantOccupancyRequest extends FormRequest
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
            'flat_id'    => ['required', 'exists:flats,id'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'flat_id.required' => 'Please select a flat.',
            'flat_id.exists' => 'The selected flat does not exist.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateOverlapping($validator);
        });
    }

    /**
     * Validate overlapping occupancy.
     */
    protected function validateOverlapping($validator): void
    {
        $building = $this->route('building');
        $tenant = $this->route('tenant');
        $flatId = $this->input('flat_id');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        // Ensure selected flat belongs to this building
        $flat = $building->flats()->whereKey($flatId)->first();
        if (!$flat) {
            $validator->errors()->add('flat_id', 'The selected flat does not belong to this building.');
            return;
        }

        // Check for overlapping occupancy for the same flat and tenant
        $overlap = DB::table('flat_tenant')
            ->where('flat_id', $flat->id)
            ->where('tenant_id', $tenant->id)
            ->where(function($q) use ($startDate, $endDate) {
                if (is_null($endDate)) {
                    // If new assignment is open-ended, block if any existing assignment ends after or at start_date
                    $q->where(function($x) use ($startDate) {
                        $x->whereNull('end_date')
                          ->orWhere('end_date', '>=', $startDate);
                    });
                } else {
                    // If new assignment has end_date, block if any existing assignment overlaps
                    $q->where(function($x) use ($startDate, $endDate) {
                        $x->where(function($y) use ($startDate, $endDate) {
                            $y->whereNull('end_date')
                              ->orWhere('end_date', '>=', $startDate);
                        })
                        ->where(function($y) use ($endDate) {
                            $y->whereNull('start_date')
                              ->orWhere('start_date', '<=', $endDate);
                        });
                    });
                }
            })->exists();

        if ($overlap) {
            $validator->errors()->add('start_date', 'Overlaps with an existing assignment for this tenant on this flat.');
        }
    }
}