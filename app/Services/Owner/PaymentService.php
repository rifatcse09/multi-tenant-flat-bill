<?php

namespace App\Services\Owner;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Add a payment and update the bill status.
     * @param int $ownerId Only allow paying bills that belong to this owner.
     * @param int $billId
     * @param float $amount
     * @param string $paidAt  // 'Y-m-d H:i:s' or 'Y-m-d'
     * @param string|null $method
     * @param string|null $reference
     * @param array|null $meta
     * @param bool $allowOverpay  If false, validation error on over-payment.
     */
    public function addPayment(
        int $ownerId,
        int $billId,
        float $amount,
        string $paidAt,
        ?string $method = null,
        ?string $ref = null,
        bool $allowOverpay = false
    ): Payment {
        $bill = Bill::where('owner_id', $ownerId)->findOrFail($billId);

        // Compute current outstanding before this payment
        $paidTotal = (float) $bill->payments()->sum('amount');
        $totalDue  = max(0.0, ($bill->amount + $bill->due_carry_forward) - $paidTotal);

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than 0.']);
        }
        if (!$allowOverpay && $amount > $totalDue) {
            throw ValidationException::withMessages(['amount' => "Payment exceeds remaining due (".number_format($totalDue,2).")."]);
        }

        return DB::transaction(function () use ($bill, $amount, $paidAt, $method, $ref) {
            $payment = $bill->payments()->create([
                'amount'    => $amount,
                'paid_at'   => date('Y-m-d H:i:s', strtotime($paidAt)),
                'method'    => $method,
                'ref' => $ref,
            ]);

            // Recalculate bill status
            $paid = (float) $bill->payments()->sum('amount');
            $gross = (float) $bill->amount + (float) $bill->due_carry_forward;

            if ($paid >= $gross) {
                $bill->status = 'paid';
            } elseif ($paid > 0) {
                $bill->status = 'partial';
            } else {
                $bill->status = 'unpaid';
            }
            $bill->save();

            // (Optional) fire events/notifications here
            // event(new \App\Events\BillPaidOrUpdated($bill, $payment));

            return $payment;
        });
    }
}