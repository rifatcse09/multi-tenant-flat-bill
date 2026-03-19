<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $bills = Bill::withoutGlobalScopes()
            ->whereIn('status', ['unpaid', 'partial'])
            ->take(5)
            ->get();

        foreach ($bills as $bill) {
            $totalDue = (float) $bill->amount + (float) $bill->due_carry_forward;
            $paid = (float) $bill->payments()->sum('amount');
            $remaining = max(0, $totalDue - $paid);
            if ($remaining <= 0) continue;

            // Pay partial or full for some bills
            $amount = $remaining >= 500 ? min(500, $remaining) : $remaining;
            Payment::create([
                'bill_id' => $bill->id,
                'amount' => $amount,
                'paid_at' => Carbon::now()->subDays(rand(1, 10)),
                'method' => 'cash',
            ]);

            $bill->refresh();
            $newPaid = (float) $bill->payments()->sum('amount');
            $status = $newPaid >= $totalDue ? 'paid' : ($newPaid > 0 ? 'partial' : 'unpaid');
            $bill->update(['status' => $status]);
        }
    }
}
