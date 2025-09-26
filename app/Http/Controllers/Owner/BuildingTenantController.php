<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Services\Owner\BuildingTenantService;
use Illuminate\Http\Request;

class BuildingTenantController extends Controller
{
    protected BuildingTenantService $buildingTenantService;

    public function __construct(BuildingTenantService $buildingTenantService)
    {
        $this->buildingTenantService = $buildingTenantService;
    }

    /**
     * Display tenants for a building.
     */
    public function index(Building $building)
    {
        // OwnerScope on Building guarantees this building belongs to the logged-in owner
        $tenants = $this->buildingTenantService->getTenantsWithOccupancy($building);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.index', compact('building', 'tenants', 'stats'));
    }

    /**
     * Show current occupancies.
     */
    public function currentOccupancies(Building $building)
    {
        $occupancies = $this->buildingTenantService->getCurrentOccupancies($building);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.current-occupancies', compact('building', 'occupancies', 'stats'));
    }

    /**
     * Show vacant flats.
     */
    public function vacantFlats(Building $building)
    {
        $vacantFlats = $this->buildingTenantService->getVacantFlats($building);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.vacant-flats', compact('building', 'vacantFlats', 'stats'));
    }

    /**
     * Show occupied flats.
     */
    public function occupiedFlats(Building $building)
    {
        $occupiedFlats = $this->buildingTenantService->getOccupiedFlats($building);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.occupied-flats', compact('building', 'occupiedFlats', 'stats'));
    }

    /**
     * Search tenants.
     */
    public function search(Building $building, Request $request)
    {
        $query = $request->get('q', '');
        $tenants = $this->buildingTenantService->searchTenants($building, $query);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.search', compact('building', 'tenants', 'query', 'stats'));
    }

    /**
     * Show unassigned tenants.
     */
    public function unassigned(Building $building)
    {
        $unassignedTenants = $this->buildingTenantService->getUnassignedTenants($building);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.unassigned', compact('building', 'unassignedTenants', 'stats'));
    }

    /**
     * Show occupancy timeline.
     */
    public function timeline(Building $building, Request $request)
    {
        $days = $request->get('days', 30);
        $timeline = $this->buildingTenantService->getOccupancyTimeline($building, $days);
        $stats = $this->buildingTenantService->getBuildingTenantStats($building);

        return view('owner.buildings.tenants.timeline', compact('building', 'timeline', 'days', 'stats'));
    }
}