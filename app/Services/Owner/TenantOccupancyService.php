<?php

namespace App\Services\Owner;

use App\Models\Building;
use App\Models\Tenant;
use App\Models\Flat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TenantOccupancyService
{
    /**
     * Check if tenant is approved for the building.
     */
    public function ensureApproved(Building $building, Tenant $tenant): void
    {
        $approved = DB::table('building_tenant')
            ->where('building_id', $building->id)
            ->where('tenant_id', $tenant->id)
            ->exists();

        if (!$approved) {
            throw new \Exception('Tenant is not approved by Admin for this building.');
        }
    }

    /**
     * Get all occupancies for a tenant in a building.
     */
    public function getOccupanciesForTenant(Building $building, Tenant $tenant): Collection
    {
        return collect(DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->orderByDesc('ft.start_date')
            ->select('ft.id', 'f.flat_number', 'ft.start_date', 'ft.end_date', 'ft.flat_id')
            ->get());
    }

    /**
     * Get flats available for assignment in a building.
     */
    public function getAvailableFlats(Building $building): Collection
    {
        return collect($building->flats()->orderBy('flat_number')->get(['id', 'flat_number']));
    }

    /**
     * Create a new occupancy record.
     */
    public function createOccupancy(Building $building, Tenant $tenant, array $data): bool
    {
        // Ensure selected flat belongs to this building (and this owner)
        $flat = $building->flats()->whereKey($data['flat_id'])->firstOrFail();

        // Check for overlapping occupancies
        $this->validateNoOverlap($flat->id, $data['start_date'], $data['end_date'] ?? null, $tenant->id);

        DB::table('flat_tenant')->insert([
            'flat_id' => $flat->id,
            'tenant_id' => $tenant->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Get occupancy record for editing.
     */
    public function getOccupancyRecord(Building $building, Tenant $tenant, int $pivotId): ?object
    {
        return DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('ft.id', $pivotId)
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->select('ft.*', 'f.flat_number')
            ->first();
    }

    /**
     * Update an occupancy record.
     */
    public function updateOccupancy(Building $building, Tenant $tenant, int $pivotId, array $data): bool
    {
        // Verify pivot belongs to this building+tenant
        $exists = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('ft.id', $pivotId)
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->exists();

        if (!$exists) {
            throw new \Exception('Occupancy record not found.');
        }

        // Ensure new flat is inside this building
        $flat = $building->flats()->whereKey($data['flat_id'])->firstOrFail();

        // Check for overlapping occupancies (excluding current record)
        $this->validateNoOverlap($flat->id, $data['start_date'], $data['end_date'] ?? null, $tenant->id, $pivotId);

        DB::table('flat_tenant')->where('id', $pivotId)->update([
            'flat_id' => $flat->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Delete an occupancy record.
     */
    public function deleteOccupancy(Building $building, Tenant $tenant, int $pivotId): bool
    {
        $deleted = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('ft.id', $pivotId)
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->delete();

        if (!$deleted) {
            throw new \Exception('Occupancy record not found.');
        }

        return true;
    }

    /**
     * End an occupancy (set end_date to today or specified date).
     */
    public function endOccupancy(Building $building, Tenant $tenant, int $pivotId, ?string $endDate = null): bool
    {
        $endDate = $endDate ?: Carbon::now()->toDateString();

        $updated = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('ft.id', $pivotId)
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->whereNull('ft.end_date')
            ->update([
                'end_date' => $endDate,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            throw new \Exception('Active occupancy record not found.');
        }

        return true;
    }

    /**
     * Get current active occupancy for a tenant in a building.
     */
    public function getCurrentOccupancy(Building $building, Tenant $tenant): ?object
    {
        return DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->whereNull('ft.end_date')
            ->select('ft.*', 'f.flat_number')
            ->first();
    }

    /**
     * Get occupancy statistics for a tenant in a building.
     */
    public function getOccupancyStats(Building $building, Tenant $tenant): array
    {
        $occupancies = $this->getOccupanciesForTenant($building, $tenant);

        $totalOccupancies = $occupancies->count();
        $currentOccupancies = $occupancies->where('end_date', null)->count();
        $pastOccupancies = $totalOccupancies - $currentOccupancies;

        $totalDuration = 0;
        foreach ($occupancies as $occupancy) {
            $start = Carbon::parse($occupancy->start_date);
            $end = $occupancy->end_date ? Carbon::parse($occupancy->end_date) : Carbon::now();
            $totalDuration += $start->diffInDays($end);
        }

        return [
            'total_occupancies' => $totalOccupancies,
            'current_occupancies' => $currentOccupancies,
            'past_occupancies' => $pastOccupancies,
            'total_days' => $totalDuration,
            'average_duration' => $totalOccupancies > 0 ? round($totalDuration / $totalOccupancies) : 0,
        ];
    }

    /**
     * Validate that there's no overlapping occupancy.
     */
    protected function validateNoOverlap(int $flatId, string $startDate, ?string $endDate, int $tenantId, ?int $excludePivotId = null): void
    {
        $query = DB::table('flat_tenant')
            ->where('flat_id', $flatId)
            ->where('tenant_id', '!=', $tenantId);

        if ($excludePivotId) {
            $query->where('id', '!=', $excludePivotId);
        }

        $overlapping = $query->where(function ($query) use ($startDate, $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                // Case 1: Existing occupancy starts before our end date (or no end date)
                $q->where('start_date', '<=', $endDate ?: '9999-12-31');
            })->where(function ($q) use ($startDate) {
                // Case 2: Existing occupancy ends after our start date (or no end date)
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $startDate);
            });
        })->exists();

        if ($overlapping) {
            throw new \Exception('This flat is already occupied during the selected period.');
        }
    }
}