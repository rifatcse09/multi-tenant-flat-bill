<?php

namespace App\Services\Owner;

use App\Models\Building;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BuildingService
{
    /**
     * Get paginated buildings for owner with search functionality.
     */
    public function paginateBuildingsForOwner(int $ownerId, string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        $query = Building::where('owner_id', $ownerId)
                        ->withCount(['flats', 'tenants']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get building statistics for owner.
     */
    public function getOwnerBuildingStats(int $ownerId): array
    {
        $buildings = Building::where('owner_id', $ownerId);
        $totalBuildings = $buildings->count();

        $totalFlats = DB::table('flats')
            ->join('buildings', 'buildings.id', '=', 'flats.building_id')
            ->where('buildings.owner_id', $ownerId)
            ->count();

        $occupiedFlats = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->join('buildings as b', 'b.id', '=', 'f.building_id')
            ->where('b.owner_id', $ownerId)
            ->whereNull('ft.end_date')
            ->distinct('f.id')
            ->count('f.id');

        $approvedTenants = DB::table('building_tenant as bt')
            ->join('buildings as b', 'b.id', '=', 'bt.building_id')
            ->where('b.owner_id', $ownerId)
            ->count();

        return [
            'total_buildings' => $totalBuildings,
            'total_flats' => $totalFlats,
            'occupied_flats' => $occupiedFlats,
            'vacant_flats' => $totalFlats - $occupiedFlats,
            'approved_tenants' => $approvedTenants,
            'occupancy_rate' => $totalFlats > 0 ? round(($occupiedFlats / $totalFlats) * 100, 1) : 0,
        ];
    }

    /**
     * Get building with detailed information.
     */
    public function getBuildingWithDetails(Building $building): Building
    {
        return $building->load([
            'flats' => function ($query) {
                $query->orderBy('flat_number')->withCount('tenants');
            },
            'tenants' => function ($query) {
                $query->orderBy('name');
            }
        ]);
    }

    /**
     * Search buildings for owner.
     */
    public function searchBuildingsForOwner(int $ownerId, string $query): Collection
    {
        return Building::where('owner_id', $ownerId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            })
            ->withCount(['flats', 'tenants'])
            ->orderBy('name')
            ->get();
    }
}