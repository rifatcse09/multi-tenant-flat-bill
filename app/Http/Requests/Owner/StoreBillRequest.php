<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'owner';
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert month input to first day of month if needed
        if ($this->filled('month')) {
            $monthValue = $this->input('month');

            // If input is like "2024-01", convert to "2024-01-01"
            if (strlen($monthValue) === 7 && preg_match('/^\d{4}-\d{2}$/', $monthValue)) {
                $this->merge([
                    'month' => $monthValue . '-01'
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'flat_id' => ['required', 'exists:flats,id'],
            'bill_category_id' => ['required', 'exists:bill_categories,id'],
            'month' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'notes' => ['nullable', 'string', 'max:500'],
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
            'month.required' => 'Bill month is required.',
            'month.date' => 'Please enter a valid month.',
            'amount.required' => 'Bill amount is required.',
            'amount.numeric' => 'Bill amount must be a valid number.',
            'amount.min' => 'Bill amount must be at least $0.01.',
            'amount.max' => 'Bill amount cannot exceed $999,999.99.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Verify the flat belongs to the authenticated owner
            if ($this->filled('flat_id')) {
                $flat = \App\Models\Flat::where('id', $this->flat_id)
                    ->where('owner_id', auth()->id())
                    ->first();

                if (!$flat) {
                    $validator->errors()->add('flat_id', 'You can only create bills for your own flats.');
                }
            }

            // Verify the category belongs to the authenticated owner
            if ($this->filled('bill_category_id')) {
                $category = \App\Models\BillCategory::where('id', $this->bill_category_id)
                    ->where('owner_id', auth()->id())
                    ->first();

                if (!$category) {
                    $validator->errors()->add('bill_category_id', 'You can only use your own bill categories.');
                }
            }
        });
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

        return $validated;
    }
}
