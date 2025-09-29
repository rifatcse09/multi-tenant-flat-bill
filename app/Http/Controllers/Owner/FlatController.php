<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreFlatRequest;
use App\Http\Requests\Owner\UpdateFlatRequest;
use App\Models\Building;
use App\Models\Flat;
use App\Services\Owner\FlatService;

class FlatController extends Controller
{
    protected FlatService $flatService;

    public function __construct(FlatService $flatService)
    {
        $this->flatService = $flatService;
    }

    // List flats for a given building
    public function indexByBuilding(Building $building)
    {
        $flats = $this->flatService->getFlatsByBuilding($building);
        $stats = $this->flatService->getBuildingFlatStats($building);

        return view('owner.flats.index', compact('building', 'flats', 'stats'));
    }

    // Show create form pre-linked to building
    public function createForBuilding(Building $building)
    {
        return view('owner.flats.create', compact('building'));
    }

    // Store flat under this building
    public function storeForBuilding(StoreFlatRequest $request, Building $building)
    {
        $data = $request->validated();

        $this->flatService->createFlat($building, $data);

        return redirect()
            ->route('owner.buildings.flats.index', $building)
            ->with('ok', 'Flat created successfully');
    }

    // Edit/update/destroy (flat is auto-scoped by OwnerScope)
    public function edit(Flat $flat)  {
        return view('owner.flats.edit', compact('flat'));
    }

    public function update(UpdateFlatRequest $request, Flat $flat)
    {
        $data = $request->validated();

        $this->flatService->updateFlat($flat, $data);

        return redirect()
            ->route('owner.buildings.flats.index', $flat->building)
            ->with('ok', 'Flat updated successfully');
    }

    public function destroy(Flat $flat)
    {
        if (!$this->flatService->canDeleteFlat($flat)) {
            return back()
                ->with('error', 'Cannot delete flat with active tenants');
        }

        $building = $flat->building;
        $this->flatService->deleteFlat($flat);

        return redirect()
            ->route('owner.buildings.flats.index', $building)
            ->with('ok', 'Flat deleted successfully');
    }
}