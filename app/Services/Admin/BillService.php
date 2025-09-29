<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Flat;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BillService
{
    public function createMonthlyBill(int $ownerId, int $flatId, int $categoryId, string $monthYmd, float $amount, ?string $notes = null): Bill
    {
        $month = Carbon::parse($monthYmd)->startOfMonth()->toDateString();

        // 1) ensure unique (owner,flat,category,month)
        $exists = Bill::where('owner_id', $ownerId)->where([
            ['flat_id', '=', $flatId],
            ['bill_category_id', '=', $categoryId],
            ['month', '=', $month],
        ])->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'month' => 'Bill already exists for this flat, category & month.'
            ]);
        }

        // 2) scope checks
        $flat = Flat::where('owner_id', $ownerId)->findOrFail($flatId);


        // 3) find snapshot tenant for that month
        $tenant = $flat->tenantForMonth($month);

        // 4) compute carry-forward (same flat+category, older months) - Fixed typo
        $prevBills = Bill::where('owner_id', $ownerId)
            ->where('flat_id', $flatId)
            ->where('bill_category_id', $categoryId)
            ->where('status', 'unpaid')  // Fixed typo: was 'statys'
            ->where('month', '<', $month)
            ->orderBy('month')
            ->get();

        $carry = 0.0;
        foreach ($prevBills as $b) {
            $paid = $b->payments()->sum('amount');
            $due  = max(0, ($b->amount + $b->due_carry_forward) - $paid);
            $carry += $due;
        }

        // 5) create bill
        return Bill::create([
            'owner_id'         => $ownerId,
            'flat_id'          => $flatId,
            'bill_category_id' => $categoryId,
            'tenant_id'        => $tenant?->id,
            'month'            => $month,
            'amount'           => $amount,
            'due_carry_forward'=> $carry,
            'status'           => 'unpaid',
            'notes'            => $notes,
        ]);
    }

    /**
     * Get bills for a specific owner with optional filters.
     */
    public function getBillsForOwner(int $ownerId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Bill::where('owner_id', $ownerId)
            ->with(['flat.building', 'category', 'tenant', 'payments']);

        if (isset($filters['flat_id'])) {
            $query->where('flat_id', $filters['flat_id']);
        }

        if (isset($filters['category_id'])) {
            $query->where('bill_category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['month'])) {
            $query->where('month', $filters['month']);
        }

        if (isset($filters['bill_to'])) {
            $query->where('bill_to', $filters['bill_to']);
        }

        return $query->orderBy('month', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Calculate bill summary.
     */
    public function calculateBillSummary(Bill $bill): array
    {
        $totalPaid = (float) $bill->payments()->sum('amount');
        $totalDue = (float) $bill->amount + (float) $bill->due_carry_forward;
        $remaining = max(0.0, $totalDue - $totalPaid);

        return [
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'remaining' => $remaining,
            'is_fully_paid' => $remaining <= 0,
            'overpaid' => $totalPaid > $totalDue ? $totalPaid - $totalDue : 0,
        ];
    }

    /**
     * Update bill status based on payments.
     */
    public function updateBillStatus(Bill $bill): void
    {
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
    }
}