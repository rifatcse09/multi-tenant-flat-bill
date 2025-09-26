<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignTenantRequest;
use App\Models\Building;
use App\Models\Tenant;
use App\Services\Admin\BuildingTenantService;
use Illuminate\Http\Request;

class BuildingTenantController extends Controller
{
    protected BuildingTenantService $buildingTenantService;

    public function __construct(BuildingTenantService $buildingTenantService)
    {
        $this->buildingTenantService = $buildingTenantService;
    }

    /**
     * Display tenants assigned to a building with pagination.
     */
    public function index(Building $building, Request $request)
    {
        $search = trim($request->get('q', ''));
        $tenants = $this->buildingTenantService->paginateBuildingTenants($building, $search);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('admin.buildings.tenants.index', compact('building', 'tenants', 'stats'));
    }

    /**
     * Show form to assign tenant to building.
     */
    public function create(Building $building)
    {
        $availableTenants = $this->buildingTenantService->getAvailableTenants($building);

        return view('admin.buildings.tenants.create', compact('building', 'availableTenants'));
    }

    /**
     * Assign tenant to building.
     */
    public function store(AssignTenantRequest $request, Building $building)
    {
        $tenant = Tenant::findOrFail($request->validated()['tenant_id']);

        try {
            $this->buildingTenantService->assignTenantToBuilding($building, $tenant);

            return redirect()
                ->route('admin.buildings.tenants.index', $building)
                ->with('ok', "Tenant '{$tenant->name}' assigned to building successfully.");
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove tenant from building.
     */
    public function destroy(Building $building, Tenant $tenant)
    {
        try {
            $this->buildingTenantService->removeTenantFromBuilding($building, $tenant);

            return back()->with('ok', "Tenant '{$tenant->name}' removed from building successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Search tenants in building.
     */
    public function search(Building $building, Request $request)
    {
        $query = trim($request->get('q', ''));

        if ($query) {
            $tenants = $this->buildingTenantService->searchBuildingTenants($building, $query);
            $stats = $this->buildingTenantService->getBuildingTenantStats($building);
            return view('admin.buildings.tenants.search', compact('building', 'tenants', 'query', 'stats'));
        }

        return redirect()->route('admin.buildings.tenants.index', $building);
    }
}