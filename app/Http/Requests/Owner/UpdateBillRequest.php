<?php

namespace App\Http\Requests\Owner;

use App\Models\Bill;
use App\Models\Flat;
use App\Models\BillCategory;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $bill = $this->route('bill');
        return auth()->check() &&
               auth()->user()->role === 'owner' &&
               $bill->owner_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'flat_id'          => ['required', 'exists:flats,id'],
            'bill_category_id' => ['required', 'exists:bill_categories,id'],
            'month'            => ['required', 'date'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:1000'],
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
            'bill_category_id.required' => 'Please select a bill category.',
            'bill_category_id.exists' => 'The selected category does not exist.',
            'month.required' => 'Please select a month.',
            'month.date' => 'Please enter a valid date.',
            'amount.required' => 'Please enter the bill amount.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than or equal to 0.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateOwnership($validator);
            $this->validateTenantAssignment($validator);
            $this->validateUniqueBill($validator);
            $this->validateBillStatus($validator);
        });
    }

    /**
     * Validate that the flat and category belong to the authenticated owner.
     */
    protected function validateOwnership($validator): void
    {
        $ownerId = auth()->id();

        // Check flat ownership
        if ($this->filled('flat_id')) {
            $flat = Flat::where('id', $this->flat_id)
                       ->where('owner_id', $ownerId)
                       ->first();

            if (!$flat) {
                $validator->errors()->add('flat_id', 'You can only update bills for your own flats.');
            }
        }

        // Check category ownership
        if ($this->filled('bill_category_id')) {
            $category = BillCategory::where('id', $this->bill_category_id)
                                   ->where('owner_id', $ownerId)
                                   ->first();

            if (!$category) {
                $validator->errors()->add('bill_category_id', 'You can only use your own bill categories.');
            }
        }
    }

    /**
     * Validate that the flat has an assigned tenant for the bill month.
     */
    protected function validateTenantAssignment($validator): void
    {
        if ($this->filled(['flat_id', 'month'])) {
            $month = Carbon::parse($this->month)->startOfMonth()->toDateString();
            $flat = Flat::find($this->flat_id);

            if ($flat) {
                // Check if flat has a tenant assigned for this month
                $tenant = $flat->tenantForMonth($month);

                if (!$tenant) {
                    $validator->errors()->add('flat_id', 'Cannot update bill for this flat. No tenant is assigned for ' . Carbon::parse($month)->format('F Y') . '.');
                }
            }
        }
    }

    /**
     * Validate that no duplicate bill exists for the same flat, category, and month.
     */
    protected function validateUniqueBill($validator): void
    {
        if ($this->filled(['flat_id', 'bill_category_id', 'month'])) {
            $bill = $this->route('bill');
            $month = Carbon::parse($this->month)->startOfMonth()->toDateString();

            $exists = Bill::where('owner_id', auth()->id())
                         ->where('flat_id', $this->flat_id)
                         ->where('bill_category_id', $this->bill_category_id)
                         ->where('month', $month)
                         ->where('id', '!=', $bill->id)
                         ->exists();

            if ($exists) {
                $validator->errors()->add('month', 'A bill already exists for this flat, category, and month.');
            }
        }
    }

    /**
     * Validate that the bill can be updated based on its status.
     */
    protected function validateBillStatus($validator): void
    {
        $bill = $this->route('bill');

        // Only allow updates to unpaid bills
        if ($bill->status !== 'unpaid') {
            $validator->errors()->add('bill', 'Only unpaid bills can be updated. This bill has status: ' . $bill->status);
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert month input to first day of month if needed
        if ($this->filled('month') && strlen($this->month) === 7) {
            // If input is like "2024-01", convert to "2024-01-01"
            $this->merge([
                'month' => $this->month . '-01'
            ]);
        }
    }

    /**
     * Get validated data with processed month.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Ensure month is first day of month
        if (isset($validated['month'])) {
            $validated['month'] = Carbon::parse($validated['month'])->startOfMonth()->toDateString();
        }




}    }        return $validated;
