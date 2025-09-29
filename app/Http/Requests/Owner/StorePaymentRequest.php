<?php

// app/Http/Requests/Owner/StorePaymentRequest.php
namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'bill_id'   => ['required','exists:bills,id'],
            'amount'    => ['required','numeric','min:0.01'],
            'paid_at'   => ['required','date'],
            'method'    => ['nullable','string','max:50'],
            'reference' => ['nullable','string','max:120'],
        ];
    }
}