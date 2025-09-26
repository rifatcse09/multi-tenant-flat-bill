<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBuildingRequest;
use App\Http\Requests\Admin\UpdateBuildingRequest;
use App\Models\Building;
use App\Services\Admin\BuildingService;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    protected BuildingService $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Display a listing of buildings with pagination and search.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));
        $buildings = $this->buildingService->paginateBuildings($search);
        $stats = $this->buildingService->getAdminDashboardStats();

        return view('admin.buildings.index', compact('buildings', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new building.
     */
    public function create()
    {
        $owners = $this->buildingService->getAllOwners();
        return view('admin.buildings.create', compact('owners'));
    }

    /**
     * Store a newly created building.
     */
    public function store(StoreBuildingRequest $request)
    {
        $building = $this->buildingService->createBuilding($request->validated());

        return redirect()
            ->route('admin.buildings.index')
            ->with('ok', 'Building created successfully');
    }

    /**
     * Display the specified building.
     */
    public function show(Building $building)
    {
        $building = $this->buildingService->getBuildingWithDetails($building);
        $stats = $this->buildingService->getBuildingStats($building);

        return view('admin.buildings.show', compact('building', 'stats'));
    }

    /**
     * Show the form for editing the building.
     */
    public function edit(Building $building)
    {
        $owners = $this->buildingService->getAllOwners();
        return view('admin.buildings.edit', compact('building', 'owners'));
    }

    /**
     * Update the specified building.
     */
    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $this->buildingService->updateBuilding($building, $request->validated());

        return redirect()
            ->route('admin.buildings.index')
            ->with('ok', 'Building updated successfully');
    }

    /**
     * Remove the specified building.
     */
    public function destroy(Building $building)
    {
        try {
            $this->buildingService->deleteBuilding($building);
            return redirect()
                ->route('admin.buildings.index')
                ->with('ok', 'Building deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}