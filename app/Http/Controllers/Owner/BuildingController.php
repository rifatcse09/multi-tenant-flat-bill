<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Services\Owner\BuildingService as OwnerBuildingService;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    protected OwnerBuildingService $buildingService;

    public function __construct(OwnerBuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Display owner's buildings with search and pagination.
     */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $buildings = $this->buildingService->paginateBuildingsForOwner(auth()->id(), $q);
        $stats = $this->buildingService->getOwnerBuildingStats(auth()->id());

        return view('owner.buildings.index', compact('buildings', 'q', 'stats'));
    }

    /**
     * Display the specified building.
     */
    public function show(Building $building)
    {
        // OwnerScope ensures only owner's buildings are accessible
        $building = $this->buildingService->getBuildingWithDetails($building);
        $stats = $this->buildingService->getOwnerBuildingStats(auth()->id());

        return view('owner.buildings.show', compact('building', 'stats'));
    }

    /**
     * Search buildings for owner.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q) {
            $buildings = $this->buildingService->searchBuildingsForOwner(auth()->id(), $q);
            return view('owner.buildings.search', compact('buildings', 'q'));
        }

        return redirect()->route('owner.buildings.index');
    }
}