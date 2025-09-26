<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Http\Request;

class BuildingTenantController extends Controller
{
    public function index(Building $building)
    {
        // Admin sees all; building could have OwnerScope—pull unscoped if needed:
        $building = Building::withoutGlobalScopes()->with(['owner','tenants'])->findOrFail($building->id);

        return view('admin.buildings.tenants.index', [
            'building' => $building,
            'tenants'  => $building->tenants()->orderBy('name')->paginate(20),
        ]);
    }

    public function create(Building $building)
    {
        $building = Building::withoutGlobalScopes()->with('owner')->findOrFail($building->id);

        // Suggest tenants not already assigned to this building
        $assignedIds = $building->tenants()->pluck('tenants.id')->all();
        $tenants = Tenant::whereNotIn('id', $assignedIds)->orderBy('name')->paginate(20);

        return view('admin.buildings.tenants.create', compact('building','tenants'));
    }

    public function store(Request $request, Building $building)
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);

        $data = $request->validate([
            'tenant_id'  => ['required','exists:tenants,id'],
            'start_date' => ['nullable','date'],
            'end_date'   => ['nullable','date','after_or_equal:start_date'],
        ]);

        // attach (Admin approval)
        $building->tenants()->syncWithoutDetaching([
            $data['tenant_id'] => [
                'start_date' => $data['start_date'] ?? now()->toDateString(),
                'end_date'   => $data['end_date'] ?? null,
            ]
        ]);

        return redirect()->route('admin.buildings.tenants.index', $building)
            ->with('ok','Tenant assigned to building');
    }

    public function destroy(Building $building, Tenant $tenant)
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);

        $building->tenants()->detach($tenant->id); // remove approval
        return redirect()->route('admin.buildings.tenants.index', $building)
            ->with('ok','Tenant removed from building');
    }
}