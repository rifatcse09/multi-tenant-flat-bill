<?php

namespace App\Services\Owner;

use App\Models\Bill;
use App\Models\BillAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdjustmentService
{
  public function addDue(
    int $ownerId, int $billId, float $amount, ?string $reason=null, string $type='manual_due'
  ): BillAdjustment {
    if ($amount == 0) {
      throw ValidationException::withMessages(['amount' => 'Amount cannot be zero.']);
    }
    $bill = Bill::where('owner_id',$ownerId)->findOrFail($billId);

    return DB::transaction(function () use ($bill,$amount,$reason,$type) {
      $adj = $bill->adjustments()->create(compact('amount','reason','type'));
      // recompute status
      $paid = (float)$bill->payments()->sum('amount');
      $gross = (float)$bill->gross;
      $bill->status = $paid >= $gross ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
      $bill->save();
      return $adj;
    });
  }
}