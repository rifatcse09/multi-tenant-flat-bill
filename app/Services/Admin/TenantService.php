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
     * Get tenant statistics (simplified version).
     */
    public function getTenantStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::whereHas('flats', function ($query) {
            $query->whereNull('flat_tenant.end_date');
        })->count();

        return [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'inactive_tenants' => $totalTenants - $activeTenants,
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

    /**
     * Get tenants with filters and pagination.
     */
    public function getTenantsWithFilters(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['search'] ?? '';

        return Tenant::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }


    /**
     * Get tenant with detailed information.
     */
    public function getTenantWithDetails(int $tenantId): Tenant
    {
        return Tenant::with([
            'flats' => function ($query) {
                $query->withPivot(['start_date', 'end_date'])
                      ->with('building:id,name');
            }
        ])
        ->findOrFail($tenantId);
    }

    /**
     * Search tenants for assignment.
     */
    public function searchTenantsForAssignment(string $search = '', int $limit = 20): Collection
    {
        return Tenant::when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'phone']);
    }

    /**
     * Get tenant assignment history.
     */
    public function getTenantAssignmentHistory(int $tenantId): Collection
    {
        $tenant = Tenant::findOrFail($tenantId);

        return $tenant->flats()
            ->withPivot(['start_date', 'end_date'])
            ->with('building:id,name')
            ->orderBy('flat_tenant.start_date', 'desc')
            ->get();
    }

    /**
     * Export tenants data.
     */
    public function exportTenants(array $filters = []): Collection
    {
        return Tenant::when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                      ->orWhere('email', 'like', "%{$filters['search']}%")
                      ->orWhere('phone', 'like', "%{$filters['search']}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if tenant can be deleted.
     */
    public function canDeleteTenant(Tenant $tenant): array
    {
        // Check if tenant has any active assignments
        $activeAssignments = $tenant->flats()
            ->whereNull('flat_tenant.end_date')
            ->count();

        // Check if tenant has any bills
        $billsCount = $tenant->bills()->count();

        // Check if tenant has any payments through bills
        $paymentsCount = $tenant->payments()->count();

        $canDelete = true;
        $reasons = [];

        if ($activeAssignments > 0) {
            $canDelete = false;
            $reasons[] = "Tenant has {$activeAssignments} active flat assignment(s)";
        }

        if ($billsCount > 0) {
            $canDelete = false;
            $reasons[] = "Tenant has {$billsCount} bill(s)";
        }

        if ($paymentsCount > 0) {
            $canDelete = false;
            $reasons[] = "Tenant has {$paymentsCount} payment(s)";
        }

        return [
            'can_delete' => $canDelete,
            'reasons' => $reasons,
        ];
    }
}