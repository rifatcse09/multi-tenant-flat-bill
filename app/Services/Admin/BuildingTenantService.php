<?php

namespace App\Services\Admin;

use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BuildingTenantService
{
    /**
     * Get paginated tenants for a building with search functionality.
     */
    public function paginateBuildingTenants(Building $building, string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = $building->tenants();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Get available tenants not assigned to building.
     */
    public function getAvailableTenants(Building $building): Collection
    {
        return Tenant::whereDoesntHave('buildings', function ($query) use ($building) {
            $query->where('building_id', $building->id);
        })->orderBy('name')->get();
    }

    /**
     * Assign tenant to building.
     */
    public function assignTenantToBuilding(Building $building, Tenant $tenant): bool
    {
        // Check if tenant is already assigned
        if ($building->tenants()->where('tenant_id', $tenant->id)->exists()) {
            throw new \Exception('Tenant is already assigned to this building.');
        }

        // Assign tenant to building
        $building->tenants()->attach($tenant->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Remove tenant from building.
     */
    public function removeTenantFromBuilding(Building $building, Tenant $tenant): bool
    {
        // Check if tenant has active occupancies
        $hasActiveOccupancies = $this->hasActiveOccupancies($building, $tenant);

        if ($hasActiveOccupancies) {
            throw new \Exception('Cannot remove tenant with active flat occupancies.');
        }

        // Remove tenant from building
        $building->tenants()->detach($tenant->id);

        return true;
    }

    /**
     * Check if tenant has active occupancies in building.
     */
    public function hasActiveOccupancies(Building $building, Tenant $tenant): bool
    {
        return DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->whereNull('ft.end_date')
            ->exists();
    }

    /**
     * Get building tenant statistics.
     */
    public function getBuildingTenantStats(Building $building): array
    {
        $totalTenants = $building->tenants()->count();
        $totalFlats = $building->flats()->count();

        $occupiedFlats = $building->flats()
            ->whereHas('tenants', function ($query) {
                $query->whereNull('flat_tenant.end_date');
            })->count();

        $activeTenants = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNull('ft.end_date')
            ->distinct('ft.tenant_id')
            ->count('ft.tenant_id');

        return [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'total_flats' => $totalFlats,
            'occupied_flats' => $occupiedFlats,
            'vacant_flats' => $totalFlats - $occupiedFlats,
            'occupancy_rate' => $totalFlats > 0 ? round(($occupiedFlats / $totalFlats) * 100, 1) : 0,
        ];
    }

    /**
     * Search tenants in building.
     */
    public function searchBuildingTenants(Building $building, string $query): Collection
    {
        return $building->tenants()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->get();
    }
}