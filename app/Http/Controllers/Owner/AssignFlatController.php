<?php

// app/Http/Controllers/Owner/AssignFlatController.php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignFlatController extends Controller
{
    public function create(Building $building, Tenant $tenant)
    {
        // Ensure this tenant is approved for this building (exists in building_tenant)
        $approved = DB::table('building_tenant')
            ->where('building_id', $building->id)
            ->where('tenant_id', $tenant->id)
            ->exists();

        abort_unless($approved, 403, 'Tenant is not approved by Admin for this building.');

        // Flats under this building (owner scoped)
        $flats = $building->flats()->orderBy('flat_number')->get(['id','flat_number']);
        return view('owner.buildings.tenants.assign', compact('building','tenant','flats'));
    }

    public function store(Request $request, Building $building, Tenant $tenant)
    {
        $data = $request->validate([
            'flat_id'    => ['required','exists:flats,id'],
            'start_date' => ['required','date'],
            'end_date'   => ['nullable','date','after_or_equal:start_date'],
        ]);

        // Ensure flat belongs to this building (and thus to this owner)
        $flat = $building->flats()->whereKey($data['flat_id'])->firstOrFail();

        // Ensure tenant is approved for this building
        $approved = DB::table('building_tenant')
            ->where('building_id', $building->id)
            ->where('tenant_id', $tenant->id)
            ->exists();
        abort_unless($approved, 403);

        // Optional: prevent overlapping occupancy on this flat
        $overlap = DB::table('flat_tenant')
            ->where('flat_id', $flat->id)
            ->where(function ($q) use ($data) {
                $start = $data['start_date'];
                $end   = $data['end_date'] ?? null;
                if ($end) {
                    $q->where(function ($x) use ($start) {
                        $x->whereNull('end_date')->orWhere('end_date','>=',$start);
                    })->where(function ($x) use ($end) {
                        $x->whereNull('start_date')->orWhere('start_date','<=',$end);
                    });
                } else {
                    $q->where(function ($x) use ($start) {
                        $x->whereNull('end_date')->orWhere('end_date','>=',$start);
                    })->where(function ($x) use ($start) {
                        $x->whereNull('start_date')->orWhere('start_date','<=',$start);
                    });
                }
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'This flat already has an occupant overlapping the selected period.',
            ]);
        }

        // Assign
        $flat->tenants()->attach($tenant->id, [
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'] ?? null,
        ]);

        return redirect()
            ->route('owner.buildings.tenants.index', $building)
            ->with('ok', 'Tenant assigned to flat.');
    }
}