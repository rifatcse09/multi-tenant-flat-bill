<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OwnerService
{
    /**
     * Get owners with filters and pagination.
     */
    public function getOwnersWithFilters(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['search'] ?? '';

        return User::where('role', 'owner')
            ->withCount('buildings')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Create a new owner.
     */
    public function createOwner(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'owner',
        ]);
    }

    /**
     * Update an existing owner.
     */
    public function updateOwner(User $owner, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $owner->update($updateData);
        return $owner;
    }

    /**
     * Delete an owner.
     */
    public function deleteOwner(User $owner): bool
    {
        return $owner->delete();
    }

    /**
     * Check if owner can be deleted.
     */
    public function canDeleteOwner(User $owner): array
    {
        $buildingsCount = $owner->buildings()->count();

        $canDelete = $buildingsCount === 0;
        $reasons = [];

        if ($buildingsCount > 0) {
            $reasons[] = "Owner has {$buildingsCount} building(s)";
        }

        return [
            'can_delete' => $canDelete,
            'reasons' => $reasons,
        ];
    }

    /**
     * Validate owner data.
     */
    public function validateOwnerData(array $data, ?int $ownerId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        // For updates, make password optional and add unique email validation
        if ($ownerId) {
            $rules['email'][] = 'unique:users,email,' . $ownerId;
            $rules['password'] = ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()];
        } else {
            $rules['email'][] = 'unique:users';
        }

        return $rules;
    }

    /**
     * Get owner statistics.
     */
    public function getOwnerStats(): array
    {
        $totalOwners = User::where('role', 'owner')->count();
        $ownersWithBuildings = User::where('role', 'owner')
            ->whereHas('buildings')
            ->count();
        $totalBuildings = \App\Models\Building::withoutGlobalScopes()->count();

        return [
            'total_owners' => $totalOwners,
            'owners_with_buildings' => $ownersWithBuildings,
            'owners_without_buildings' => $totalOwners - $ownersWithBuildings,
            'total_buildings' => $totalBuildings,
        ];
    }

    /**
     * Get recent owners.
     */
    public function getRecentOwners(int $limit = 5): Collection
    {
        return User::where('role', 'owner')
            ->withCount('buildings')
            ->latest()
            ->limit($limit)
            ->get(['id', 'name', 'email', 'created_at']);
    }
}
