<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjustmentRequest extends FormRequest {
  public function authorize(): bool { return auth()->check() && auth()->user()->role==='owner'; }
  public function rules(): array {
    return [
      'bill_id' => ['required','exists:bills,id'],
      'amount'  => ['required','numeric','not_in:0'],
      'reason'  => ['nullable','string','max:255'],
      'type'    => ['nullable','string','max:50'],
    ];
  }
}