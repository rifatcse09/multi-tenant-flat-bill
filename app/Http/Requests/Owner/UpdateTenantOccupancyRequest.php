<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
     * Validate overlapping occupancy for updates.
     */
    protected function validateOverlapping($validator): void
    {
        $building = $this->route('building');
        $tenant = $this->route('tenant');
        $pivotId = $this->route('pivotId');
        $flatId = $this->input('flat_id');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        // Ensure selected flat belongs to this building
        $flat = $building->flats()->whereKey($flatId)->first();
        if (!$flat) {
            $validator->errors()->add('flat_id', 'The selected flat does not belong to this building.');
            return;
        }

        // Get current assignment
        $currentAssignment = DB::table('flat_tenant')->where('id', $pivotId)->first();

        // If flat_id is changed, check for open assignment on new flat
        if ($currentAssignment && $currentAssignment->flat_id != $flatId) {
            $openAssignment = DB::table('flat_tenant')
                ->where('flat_id', $flat->id)
                ->whereNull('end_date')
                ->whereNull('start_date')
                ->exists();

            if ($openAssignment) {
                $validator->errors()->add('flat_id', 'Cannot assign this flat before the previous assignment is ended.');
                return;
            }
        }

        // If updating same assignment, allow change if start_date is after previous end_date or end_date is set
        if ($currentAssignment && $currentAssignment->flat_id == $flatId) {
            if (is_null($currentAssignment->end_date) && !is_null($endDate)) {
                // Allow closing open assignment
                return;
            }
            if (!is_null($currentAssignment->end_date) && $startDate > $currentAssignment->end_date) {
                // Allow new slot after previous end_date
                return;
            }
        }

        // Check for overlapping occupancy, excluding current pivot
        $overlap = DB::table('flat_tenant')
            ->where('flat_id', $flat->id)
            ->where('tenant_id', $tenant->id)
            ->where('id', '!=', $pivotId)
            ->where(function($q) use ($startDate, $endDate) {
                if ($endDate) {
                    $q->where(function($x) use ($startDate) {
                        $x->whereNull('end_date')->orWhere('end_date', '>=', $startDate);
                    })->where(function($x) use ($endDate) {
                        $x->whereNull('start_date')->orWhere('start_date', '<=', $endDate);
                    });
                } else {
                    $q->where(function($x) use ($startDate) {
                        $x->whereNull('end_date')->orWhere('end_date', '>=', $startDate);
                    })->where(function($x) use ($startDate) {
                        $x->whereNull('start_date')->orWhere('start_date', '<=', $startDate);
                    });
                }
            })->exists();

        if ($overlap) {
            $validator->errors()->add('start_date', 'Overlaps with an existing assignment for this tenant on this flat.');
        }
    }
}