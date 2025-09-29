<?php

namespace App\Services\Owner;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
  public function addPayment(
    int $ownerId, int $billId, float $amount, string $paidAt,
    ?string $method=null, ?string $reference=null, ?array $meta=null,
    bool $allowOverpay=false
  ): Payment {
    $bill = Bill::where('owner_id',$ownerId)->findOrFail($billId);

    $paidSoFar = (float)$bill->payments()->sum('amount');
    $dueBefore = max(0.0, $bill->gross - $paidSoFar);

    if ($amount <= 0) {
      throw ValidationException::withMessages(['amount' => 'Amount must be greater than 0.']);
    }
    if (!$allowOverpay && $amount > $dueBefore) {
      throw ValidationException::withMessages(['amount' => "Payment exceeds remaining due (".number_format($dueBefore,2).")."]);
    }

    return DB::transaction(function () use ($bill,$amount,$paidAt,$method,$reference,$meta) {
      $payment = $bill->payments()->create([
        'amount' => $amount,
        'paid_at'=> date('Y-m-d H:i:s', strtotime($paidAt)),
        'method' => $method, 'reference' => $reference, 'meta' => $meta,
      ]);

      $this->recomputeBillStatus($bill->fresh('payments','adjustments'));
      return $payment;
    });
  }

  public function recomputeBillStatus(Bill $bill): void
  {
    $paid = (float)$bill->payments()->sum('amount');
    $gross = (float)$bill->gross;

    $bill->status = $paid >= $gross ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
    $bill->save();
  }
}