<?php

namespace App\Services\Admin;

use App\Models\Building;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BuildingService
{
    /**
     * Get buildings with filters and pagination.
     */
    public function getBuildingsWithFilters(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['search'] ?? '';

        return Building::withoutGlobalScopes()
            ->with('owner:id,name,email')
            ->withCount(['flats', 'tenants'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('address', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Get all owners for dropdowns.
     */
    public function getOwnersForDropdown(): Collection
    {
        return User::where('role', 'owner')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * Create a new building.
     */
    public function createBuilding(array $data): Building
    {
        return Building::withoutGlobalScopes()->create([
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
        ]);
    }

    /**
     * Update an existing building.
     */
    public function updateBuilding(Building $building, array $data): Building
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);

        $building->update([
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
        ]);

        return $building;
    }

    /**
     * Delete a building.
     */
    public function deleteBuilding(Building $building): bool
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);
        return $building->delete();
    }

    /**
     * Get building with full details.
     */
    public function getBuildingWithDetails(int $buildingId): Building
    {
        return Building::withoutGlobalScopes()
            ->with(['owner:id,name,email', 'flats', 'tenants'])
            ->withCount(['flats', 'tenants'])
            ->findOrFail($buildingId);
    }

    /**
     * Check if building can be deleted.
     */
    public function canDeleteBuilding(Building $building): array
    {
        $building = Building::withoutGlobalScopes()
            ->withCount(['flats', 'tenants'])
            ->findOrFail($building->id);

        $canDelete = true;
        $reasons = [];

        if ($building->flats_count > 0) {
            $canDelete = false;
            $reasons[] = "Building has {$building->flats_count} flat(s)";
        }

        if ($building->tenants_count > 0) {
            $canDelete = false;
            $reasons[] = "Building has {$building->tenants_count} tenant(s)";
        }

        return [
            'can_delete' => $canDelete,
            'reasons' => $reasons,
        ];
    }
}