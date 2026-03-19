<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Flat;
use App\Models\BillCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $owners = \App\Models\User::where('role', 'owner')->get();
        if ($owners->isEmpty()) return;

        foreach ($owners as $owner) {
            $flats = Flat::withoutGlobalScopes()
                ->where('owner_id', $owner->id)
                ->whereHas('tenants', fn ($q) => $q->whereNull('flat_tenant.end_date'))
                ->get();

            $categories = BillCategory::where('owner_id', $owner->id)->get();
            if ($flats->isEmpty() || $categories->isEmpty()) continue;

            $months = [
                Carbon::now()->subMonths(2)->startOfMonth(),
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->startOfMonth(),
            ];

            foreach ($flats->take(2) as $flat) {
                $tenant = $flat->tenants()->whereNull('flat_tenant.end_date')->first();
                foreach ($categories->take(2) as $category) {
                    foreach ($months as $month) {
                        Bill::firstOrCreate(
                            [
                                'flat_id' => $flat->id,
                                'bill_category_id' => $category->id,
                                'month' => $month->toDateString(),
                            ],
                            [
                                'owner_id' => $owner->id,
                                'tenant_id' => $tenant?->id,
                                'amount' => rand(500, 3000),
                                'due_carry_forward' => 0,
                                'status' => 'unpaid',
                            ]
                        );
                    }
                }
            }
        }
    }
}
