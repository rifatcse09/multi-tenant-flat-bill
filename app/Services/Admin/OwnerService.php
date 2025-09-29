<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

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
}