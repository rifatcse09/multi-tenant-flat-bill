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
    public function __construct(private BuildingService $buildingService) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => trim($request->get('q', '')),
        ];

        $buildings = $this->buildingService->getBuildingsWithFilters($filters);

        return view('admin.buildings.index', [
            'buildings' => $buildings,
            'search' => $filters['search'],
        ]);
    }

    public function create()
    {
        $owners = $this->buildingService->getOwnersForDropdown();
        return view('admin.buildings.create', compact('owners'));
    }

    public function store(StoreBuildingRequest $request)
    {
        try {
            $data = $request->validated();
            $this->buildingService->createBuilding($data);

            return redirect()->route('admin.buildings.index')
                ->with('ok', 'Building created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create building: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Building $building)
    {
        $building = $this->buildingService->getBuildingWithDetails($building->id);
        return view('admin.buildings.show', compact('building'));
    }

    public function edit(Building $building)
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);
        $owners = $this->buildingService->getOwnersForDropdown();
        return view('admin.buildings.edit', compact('building', 'owners'));
    }

    public function update(UpdateBuildingRequest $request, Building $building)
    {
        try {
            $data = $request->validated();
            $this->buildingService->updateBuilding($building, $data);

            return redirect()->route('admin.buildings.index')
                ->with('ok', 'Building updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update building: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Building $building)
    {
        try {
            $canDelete = $this->buildingService->canDeleteBuilding($building);

            if (!$canDelete['can_delete']) {
                return back()->with('error', 'Cannot delete building: ' . implode(', ', $canDelete['reasons']));
            }

            $this->buildingService->deleteBuilding($building);
            return redirect()->route('admin.buildings.index')
                ->with('ok', 'Building deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete building: ' . $e->getMessage());
        }
    }
}