<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OwnerService
{
    /**
     * Get paginated owners with search functionality.
     */
    public function paginateOwners(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = User::where('role', 'owner');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new owner.
     */
    public function createOwner(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'slug' => $data['slug'] ?? null,
            'role' => 'owner',
            'password' => Hash::make($data['password'] ?? 'password'),
        ]);
    }

    /**
     * Update an existing owner.
     */
    public function updateOwner(User $owner, array $data): bool
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'slug' => $data['slug'] ?? null,
        ];

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $owner->update($updateData);
    }

    /**
     * Delete an owner.
     */
    public function deleteOwner(User $owner): bool
    {
        // Check if owner has buildings
        if ($owner->buildings()->exists()) {
            throw new \Exception('Cannot delete owner with existing buildings.');
        }

        return $owner->delete();
    }

    /**
     * Get owner statistics.
     */
    public function getOwnerStats(User $owner): array
    {
        $totalBuildings = $owner->buildings()->count();

        $totalFlats = DB::table('flats')
            ->join('buildings', 'buildings.id', '=', 'flats.building_id')
            ->where('buildings.owner_id', $owner->id)
            ->count();

        $occupiedFlats = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->join('buildings as b', 'b.id', '=', 'f.building_id')
            ->where('b.owner_id', $owner->id)
            ->whereNull('ft.end_date')
            ->distinct('f.id')
            ->count('f.id');

        $approvedTenants = DB::table('building_tenant as bt')
            ->join('buildings as b', 'b.id', '=', 'bt.building_id')
            ->where('b.owner_id', $owner->id)
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
     * Get admin dashboard owner statistics.
     */
    public function getAdminOwnerStats(): array
    {
        $totalOwners = User::where('role', 'owner')->count();
        $ownersWithBuildings = User::where('role', 'owner')
            ->whereHas('buildings')
            ->count();

        $activeOwners = User::where('role', 'owner')
            ->whereHas('buildings.flats.tenants', function ($query) {
                $query->whereNull('flat_tenant.end_date');
            })
            ->distinct()
            ->count();

        return [
            'total_owners' => $totalOwners,
            'owners_with_buildings' => $ownersWithBuildings,
            'owners_without_buildings' => $totalOwners - $ownersWithBuildings,
            'active_owners' => $activeOwners,
            'activity_rate' => $totalOwners > 0 ? round(($activeOwners / $totalOwners) * 100, 1) : 0,
        ];
    }

    /**
     * Search owners.
     */
    public function searchOwners(string $query): Collection
    {
        return User::where('role', 'owner')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('slug', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get owners with building counts.
     */
    public function getOwnersWithBuildingCounts(): Collection
    {
        return User::where('role', 'owner')
            ->withCount(['buildings'])
            ->orderBy('name')
            ->get();
    }
}