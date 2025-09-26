<?php

namespace App\Services\Owner;

use App\Models\Building;
use App\Models\Flat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FlatService
{
    /**
     * Get paginated flats for a building.
     */
    public function getFlatsByBuilding(Building $building, int $perPage = 12): LengthAwarePaginator
    {
        return $building->flats()->latest()->paginate($perPage);
    }

    /**
     * Get flats for a building (collection).
     */
    public function getFlatsByBuildingCollection(Building $building): Collection
    {
        return $building->flats()->orderBy('flat_number')->get(['id', 'flat_number']);
    }

    /**
     * Create a new flat for a building.
     */
    public function createFlat(Building $building, array $data): Flat
    {
        return Flat::create([
            'building_id'      => $building->id,
            'owner_id'         => auth()->id(),
            'flat_number'      => $data['flat_number'],
            'flat_owner_name'  => $data['flat_owner_name'] ?? null,
            'flat_owner_phone' => $data['flat_owner_phone'] ?? null,
        ]);
    }

    /**
     * Update an existing flat.
     */
    public function updateFlat(Flat $flat, array $data): bool
    {
        return $flat->update([
            'flat_number'      => $data['flat_number'],
            'flat_owner_name'  => $data['flat_owner_name'] ?? null,
            'flat_owner_phone' => $data['flat_owner_phone'] ?? null,
        ]);
    }

    /**
     * Delete a flat.
     */
    public function deleteFlat(Flat $flat): bool
    {
        return $flat->delete();
    }

    /**
     * Check if flat number is unique within building (for creation).
     */
    public function isFlatNumberUniqueForBuilding(Building $building, string $flatNumber): bool
    {
        return !$building->flats()
            ->where('flat_number', $flatNumber)
            ->exists();
    }

    /**
     * Check if flat number is unique within building (for update).
     */
    public function isFlatNumberUniqueForUpdate(Flat $flat, string $flatNumber): bool
    {
        return !Flat::where('building_id', $flat->building_id)
            ->where('flat_number', $flatNumber)
            ->where('id', '!=', $flat->id)
            ->exists();
    }

    /**
     * Get flat statistics for a building.
     */
    public function getBuildingFlatStats(Building $building): array
    {
        $flats = $building->flats();

        return [
            'total_flats' => $flats->count(),
            'occupied_flats' => $flats->whereHas('tenants')->count(),
            'vacant_flats' => $flats->whereDoesntHave('tenants')->count(),
            'flats_with_owner_info' => $flats->whereNotNull('flat_owner_name')->count(),
        ];
    }

    /**
     * Search flats by criteria.
     */
    public function searchFlats(Building $building, string $query): Collection
    {
        return $building->flats()
            ->where(function ($q) use ($query) {
                $q->where('flat_number', 'like', "%{$query}%")
                  ->orWhere('flat_owner_name', 'like', "%{$query}%")
                  ->orWhere('flat_owner_phone', 'like', "%{$query}%");
            })
            ->orderBy('flat_number')
            ->get();
    }

    /**
     * Get flats with tenant information.
     */
    public function getFlatsWithTenants(Building $building): Collection
    {
        return $building->flats()
            ->with(['tenants' => function ($query) {
                $query->wherePivot('end_date', null); // Current tenants only
            }])
            ->orderBy('flat_number')
            ->get();
    }

    /**
     * Check if flat can be deleted (no active tenants).
     */
    public function canDeleteFlat(Flat $flat): bool
    {
        return !$flat->tenants()
            ->wherePivot('end_date', null)
            ->exists();
    }

    /**
     * Get flat with building information.
     */
    public function getFlatWithBuilding(int $flatId): ?Flat
    {
        return Flat::with('building')->find($flatId);
    }

    /**
     * Bulk create flats for a building.
     */
    public function bulkCreateFlats(Building $building, array $flatsData): int
    {
        $created = 0;

        foreach ($flatsData as $flatData) {
            if ($this->isFlatNumberUniqueForBuilding($building, $flatData['flat_number'])) {
                $this->createFlat($building, $flatData);
                $created++;
            }
        }

        return $created;
    }
}