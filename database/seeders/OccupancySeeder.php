<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OccupancySeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();
        $buildings = Building::withoutGlobalScopes()->get();

        if ($tenants->isEmpty() || $buildings->isEmpty()) {
            return;
        }

        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = null; // current occupancy

        foreach ($buildings as $building) {
            // Attach first 2 tenants to building
            foreach ($tenants->take(2) as $tenant) {
                $building->tenants()->syncWithoutDetaching([
                    $tenant->id => ['start_date' => $startDate, 'end_date' => $endDate],
                ]);
            }
        }

        // Assign tenants to flats
        $flats = Flat::withoutGlobalScopes()->get();
        $tenantIndex = 0;
        foreach ($flats as $flat) {
            if ($tenantIndex >= $tenants->count()) break;
            $tenant = $tenants[$tenantIndex];
            $flat->tenants()->syncWithoutDetaching([
                $tenant->id => ['start_date' => $startDate, 'end_date' => $endDate],
            ]);
            $tenantIndex++;
        }
    }
}
