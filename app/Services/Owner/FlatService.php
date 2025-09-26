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
     * Get building flat statistics.
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
     * Check if flat can be deleted (no active tenants).
     */
    public function canDeleteFlat(Flat $flat): bool
    {
        return !$flat->tenants()
            ->wherePivot('end_date', null)
            ->exists();
    }
}