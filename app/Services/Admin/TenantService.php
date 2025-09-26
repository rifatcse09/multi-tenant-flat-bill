<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TenantService
{
    /**
     * Get paginated tenants with search functionality.
     */
    public function paginateTenants(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = Tenant::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new tenant.
     */
    public function createTenant(array $data): Tenant
    {
        return Tenant::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
    }

    /**
     * Update an existing tenant.
     */
    public function updateTenant(Tenant $tenant, array $data): bool
    {
        return $tenant->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
    }

    /**
     * Delete a tenant.
     */
    public function deleteTenant(Tenant $tenant): bool
    {
        // Check if tenant has active occupancies
        if ($this->hasActiveOccupancies($tenant)) {
            throw new \Exception('Cannot delete tenant with active flat occupancies.');
        }

        return $tenant->delete();
    }

    /**
     * Check if tenant has active occupancies.
     */
    public function hasActiveOccupancies(Tenant $tenant): bool
    {
        return DB::table('flat_tenant')
            ->where('tenant_id', $tenant->id)
            ->whereNull('end_date')
            ->exists();
    }

    /**
     * Get tenant statistics.
     */
    public function getTenantStats(Tenant $tenant): array
    {
        $totalBuildings = $tenant->buildings()->count();

        $activeOccupancies = DB::table('flat_tenant as ft')
            ->where('tenant_id', $tenant->id)
            ->whereNull('end_date')
            ->count();

        $totalOccupancies = DB::table('flat_tenant')
            ->where('tenant_id', $tenant->id)
            ->count();

        return [
            'total_buildings' => $totalBuildings,
            'active_occupancies' => $activeOccupancies,
            'total_occupancies' => $totalOccupancies,
            'past_occupancies' => $totalOccupancies - $activeOccupancies,
        ];
    }

    /**
     * Get admin dashboard tenant statistics.
     */
    public function getAdminTenantStats(): array
    {
        $totalTenants = Tenant::count();

        $activeTenants = DB::table('flat_tenant as ft')
            ->whereNull('ft.end_date')
            ->distinct('ft.tenant_id')
            ->count('ft.tenant_id');

        $tenantsWithOccupancies = Tenant::whereHas('flats')->count();

        return [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'inactive_tenants' => $totalTenants - $activeTenants,
            'tenants_with_occupancies' => $tenantsWithOccupancies,
            'activity_rate' => $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100, 1) : 0,
        ];
    }

    /**
     * Search tenants.
     */
    public function searchTenants(string $query): Collection
    {
        return Tenant::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        })
        ->orderBy('name')
        ->get();
    }

    /**
     * Get tenants with building counts.
     */
    public function getTenantsWithBuildingCounts(): Collection
    {
        return Tenant::withCount(['buildings'])->orderBy('name')->get();
    }

    /**
     * Check if email is unique.
     */
    public function isEmailUnique(string $email, ?int $excludeTenantId = null): bool
    {
        if (empty($email)) {
            return true;
        }

        $query = Tenant::where('email', $email);

        if ($excludeTenantId) {
            $query->where('id', '!=', $excludeTenantId);
        }

        return !$query->exists();
    }
}