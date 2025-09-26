<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreTenantOccupancyRequest;
use App\Http\Requests\Owner\UpdateTenantOccupancyRequest;
use App\Models\Building;
use App\Models\Tenant;
use App\Services\Owner\TenantOccupancyService;
use Illuminate\Http\Request;

class TenantOccupancyController extends Controller
{
    protected TenantOccupancyService $occupancyService;

    public function __construct(TenantOccupancyService $occupancyService)
    {
        $this->occupancyService = $occupancyService;
    }

    public function index(Building $building, Tenant $tenant)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        $flats = $this->occupancyService->getAvailableFlats($building);
        $occupancies = $this->occupancyService->getOccupanciesForTenant($building, $tenant);
        $stats = $this->occupancyService->getOccupancyStats($building, $tenant);

        return view('owner.occupancies.index', compact('building', 'tenant', 'flats', 'occupancies', 'stats'));
    }

    public function create(Building $building, Tenant $tenant)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        $flats = $this->occupancyService->getAvailableFlats($building);
        return view('owner.occupancies.create', compact('building', 'tenant', 'flats'));
    }

    public function store(StoreTenantOccupancyRequest $request, Building $building, Tenant $tenant)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
            $this->occupancyService->createOccupancy($building, $tenant, $request->validated());

            return redirect()
                ->route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id])
                ->with('ok', 'Assignment created successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Building $building, Tenant $tenant, int $pivotId)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
            $row = $this->occupancyService->getOccupancyRecord($building, $tenant, $pivotId);

            if (!$row) {
                abort(404);
            }

            $flats = $this->occupancyService->getAvailableFlats($building);
            return view('owner.occupancies.edit', compact('building', 'tenant', 'row', 'flats'));
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
    }

    public function update(UpdateTenantOccupancyRequest $request, Building $building, Tenant $tenant, int $pivotId)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
            $this->occupancyService->updateOccupancy($building, $tenant, $pivotId, $request->validated());

            return redirect()
                ->route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id])
                ->with('ok', 'Assignment updated successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Building $building, Tenant $tenant, int $pivotId)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
            $this->occupancyService->deleteOccupancy($building, $tenant, $pivotId);

            return back()->with('ok', 'Record deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function end(Building $building, Tenant $tenant, int $pivotId, Request $request)
    {
        try {
            $this->occupancyService->ensureApproved($building, $tenant);
            $endDate = $request->input('end_date');
            $this->occupancyService->endOccupancy($building, $tenant, $pivotId, $endDate);

            return back()->with('ok', 'Occupancy ended successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}