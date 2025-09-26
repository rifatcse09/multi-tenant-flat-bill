<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Http\Requests\Admin\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\Admin\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of tenants with pagination and search.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $tenants = $this->tenantService->paginateTenants($search);
        $stats = $this->tenantService->getAdminTenantStats();

        return view('admin.tenants.index', compact('tenants', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(StoreTenantRequest $request)
    {
        $tenant = $this->tenantService->createTenant($request->validated());

        return redirect()
            ->route('admin.tenants.index')
            ->with('ok', "Tenant '{$tenant->name}' created successfully");
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant)
    {
        $stats = $this->tenantService->getTenantStats($tenant);
        $buildings = $tenant->buildings()->withCount(['flats'])->get();

        return view('admin.tenants.show', compact('tenant', 'stats', 'buildings'));
    }

    /**
     * Show the form for editing the tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $this->tenantService->updateTenant($tenant, $request->validated());

        return redirect()
            ->route('admin.tenants.index')
            ->with('ok', 'Tenant updated successfully');
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Tenant $tenant)
    {
        try {
            $this->tenantService->deleteTenant($tenant);
            return redirect()
                ->route('admin.tenants.index')
                ->with('ok', 'Tenant deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Search tenants.
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if ($query) {
            $tenants = $this->tenantService->searchTenants($query);
            return view('admin.tenants.search', compact('tenants', 'query'));
        }

        return redirect()->route('admin.tenants.index');
    }
}