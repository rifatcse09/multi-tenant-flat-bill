<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreTenantOccupancyRequest;
use App\Http\Requests\Owner\UpdateTenantOccupancyRequest;
use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantOccupancyController extends Controller
{
    private function ensureApproved(Building $building, Tenant $tenant): void
    {
        $approved = DB::table('building_tenant')
            ->where('building_id', $building->id)
            ->where('tenant_id', $tenant->id)
            ->exists();
        abort_unless($approved, 403, 'Tenant is not approved by Admin for this building.');
    }

    public function index(Building $building, Tenant $tenant)
    {
        $this->ensureApproved($building, $tenant);

        // All flats (owner’s) in this building
        $flats = $building->flats()->orderBy('flat_number')->get(['id','flat_number']);

        // All occupancies (flat_tenant rows) for this tenant in THIS building
        $occupancies = DB::table('flat_tenant as ft')
            ->join('flats as f', 'f.id', '=', 'ft.flat_id')
            ->where('f.building_id', $building->id)
            ->where('ft.tenant_id', $tenant->id)
            ->orderByDesc('ft.start_date')
            ->select('ft.id','f.flat_number','ft.start_date','ft.end_date','ft.flat_id')
            ->get();

        return view('owner.occupancies.index', compact('building','tenant','flats','occupancies'));
    }

    public function create(Building $building, Tenant $tenant)
    {
        $this->ensureApproved($building, $tenant);
        $flats = $building->flats()->orderBy('flat_number')->get(['id','flat_number']);
        return view('owner.occupancies.create', compact('building','tenant','flats'));
    }

    public function store(StoreTenantOccupancyRequest $request, Building $building, Tenant $tenant)
    {
        $this->ensureApproved($building, $tenant);
        $data = $request->validated();

        // Ensure selected flat belongs to this building (and this owner)
        $flat = $building->flats()->whereKey($data['flat_id'])->firstOrFail();

        DB::table('flat_tenant')->insert([
            'flat_id' => $flat->id,
            'tenant_id' => $tenant->id,
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'] ?? null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return redirect()->route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id])
            ->with('ok','Assignment created');
    }

    public function edit(Building $building, Tenant $tenant, int $pivotId)
    {
        $this->ensureApproved($building, $tenant);

        $row = DB::table('flat_tenant as ft')
            ->join('flats as f','f.id','=','ft.flat_id')
            ->where('ft.id',$pivotId)
            ->where('f.building_id',$building->id)
            ->where('ft.tenant_id',$tenant->id)
            ->select('ft.*','f.flat_number')
            ->first();
        abort_unless($row, 404);

        $flats = $building->flats()->orderBy('flat_number')->get(['id','flat_number']);
        return view('owner.occupancies.edit', compact('building','tenant','row','flats'));
    }

    public function update(UpdateTenantOccupancyRequest $request, Building $building, Tenant $tenant, int $pivotId)
    {
        $this->ensureApproved($building, $tenant);
        $data = $request->validated();

        // verify pivot belongs to this building+tenant
        $exists = DB::table('flat_tenant as ft')
            ->join('flats as f','f.id','=','ft.flat_id')
            ->where('ft.id',$pivotId)
            ->where('f.building_id',$building->id)
            ->where('ft.tenant_id',$tenant->id)
            ->exists();
        abort_unless($exists, 404);

        // ensure new flat is inside this building
        $flat = $building->flats()->whereKey($data['flat_id'])->firstOrFail();

        DB::table('flat_tenant')->where('id',$pivotId)->update([
            'flat_id'    => $flat->id,
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id])
            ->with('ok','Assignment updated');
    }

    public function destroy(Building $building, Tenant $tenant, int $pivotId)
    {
        $this->ensureApproved($building, $tenant);
        $deleted = DB::table('flat_tenant as ft')
            ->join('flats as f','f.id','=','ft.flat_id')
            ->where('ft.id',$pivotId)
            ->where('f.building_id',$building->id)
            ->where('ft.tenant_id',$tenant->id)
            ->delete();
        abort_unless($deleted, 404);
        return back()->with('ok','Record deleted');
    }
}