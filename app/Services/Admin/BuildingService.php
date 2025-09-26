<?php

namespace App\Services\Admin;

use App\Models\Building;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BuildingService
{
    /**
     * Get paginated buildings with search functionality.
     */
    public function paginateBuildings(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = Building::with('owner:id,name,email');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new building.
     */
    public function createBuilding(array $data): Building
    {
        return Building::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'owner_id' => $data['owner_id'],
        ]);
    }

    /**
     * Update an existing building.
     */
    public function updateBuilding(Building $building, array $data): bool
    {
        return $building->update([
            'name' => $data['name'],
            'address' => $data['address'],
            'owner_id' => $data['owner_id'],
        ]);
    }

    /**
     * Delete a building.
     */
    public function deleteBuilding(Building $building): bool
    {
        // Check if building has flats with active tenants
        $hasActiveTenants = $building->flats()
            ->whereHas('tenants', function ($query) {
                $query->whereNull('flat_tenant.end_date');
            })->exists();

        if ($hasActiveTenants) {
            throw new \Exception('Cannot delete building with active tenants.');
        }

        return $building->delete();
    }

    /**
     * Get all owners for dropdown.
     */
    public function getAllOwners(): Collection
    {
        return User::where('role', 'owner')->orderBy('name')->get(['id', 'name', 'email']);
    }

    /**
     * Get building statistics.
     */
    public function getBuildingStats(Building $building): array
    {
        $totalFlats = $building->flats()->count();
        $occupiedFlats = $building->flats()
            ->whereHas('tenants', function ($query) {
                $query->whereNull('flat_tenant.end_date');
            })->count();

        $approvedTenants = $building->tenants()->count();
        $activeTenants = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->whereNull('ft.end_date')
            ->distinct('ft.tenant_id')
            ->count('ft.tenant_id');

        return [
            'total_flats' => $totalFlats,
            'occupied_flats' => $occupiedFlats,
            'vacant_flats' => $totalFlats - $occupiedFlats,
            'approved_tenants' => $approvedTenants,
            'active_tenants' => $activeTenants,
            'occupancy_rate' => $totalFlats > 0 ? round(($occupiedFlats / $totalFlats) * 100, 1) : 0,
        ];
    }

    /**
     * Get building with detailed information.
     */
    public function getBuildingWithDetails(Building $building): Building
    {
        return $building->load([
            'owner:id,name,email',
            'flats' => function ($query) {
                $query->orderBy('flat_number');
            },
            'tenants' => function ($query) {
                $query->orderBy('name');
            }
        ]);
    }

    /**
     * Get admin dashboard building statistics.
     */
    public function getAdminDashboardStats(): array
    {
        $totalBuildings = Building::count();
        $totalFlats = DB::table('flats')->count();
        $totalOccupiedFlats = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->whereNull('ft.end_date')
            ->distinct('f.id')
            ->count('f.id');

        $totalTenants = DB::table('tenants')->count();
        $activeTenants = DB::table('flat_tenant as ft')
            ->whereNull('ft.end_date')
            ->distinct('ft.tenant_id')
            ->count('ft.tenant_id');

        return [
            'total_buildings' => $totalBuildings,
            'total_flats' => $totalFlats,
            'occupied_flats' => $totalOccupiedFlats,
            'vacant_flats' => $totalFlats - $totalOccupiedFlats,
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'overall_occupancy_rate' => $totalFlats > 0 ? round(($totalOccupiedFlats / $totalFlats) * 100, 1) : 0,
        ];
    }
}