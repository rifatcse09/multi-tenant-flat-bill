<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;

class BuildingTenantController extends Controller
{
    public function index(Building $building)
    {
       // OwnerScope on Building guarantees this building belongs to the logged-in owner
        $building->load([
            'tenants' => function ($q) {
                $q->orderBy('name');
            },
            'flats'   // we'll use this list to show current flat per tenant
        ]);

        // Preload tenant -> flats filtered to this building (to find current occupancy)
        $tenants = $building->tenants->load(['flats' => function ($q) use ($building) {
            $q->where('building_id', $building->id)->withPivot(['start_date','end_date']);
        }]);

        //dd($tenants);

        return view('owner.buildings.tenants.index', compact('building','tenants'));
    }
}