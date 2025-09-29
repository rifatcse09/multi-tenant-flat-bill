<?php

namespace App\Services\Owner;

use App\Models\Bill;
use App\Models\Flat;
use App\Models\BillCategory;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BillService
{
    public function createMonthlyBill(int $ownerId, int $flatId, int $categoryId, string $monthYmd, float $amount, ?string $notes = null): Bill
    {
        $month = Carbon::parse($monthYmd)->startOfMonth()->toDateString();

        // 1) ensure unique (owner,flat,category,month) - fix column name
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
        $category = BillCategory::where('owner_id', $ownerId)->findOrFail($categoryId);

        // 3) find snapshot tenant for that month
        $tenant = $flat->tenantForMonth($month);
        $billTo = $tenant ? 'tenant' : 'owner';

        // 4) compute carry-forward (same flat+category, older months) - fix typo in 'status'
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
}