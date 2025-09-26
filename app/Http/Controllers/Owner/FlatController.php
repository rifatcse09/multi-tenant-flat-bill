<?php
// app/Http/Controllers/Owner/FlatController.php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Flat;
use Illuminate\Http\Request;

class FlatController extends Controller
{
    // List flats for a given building
    public function indexByBuilding(Building $building)
    {
        // OwnerScope on Building ensures it’s the owner’s building
        $flats = $building->flats()->latest()->paginate(12);
        return view('owner.flats.index', compact('building','flats'));
    }

    // Show create form pre-linked to building
    public function createForBuilding(Building $building)
    {
        return view('owner.flats.create', compact('building'));
    }

    // Store flat under this building
    public function storeForBuilding(Request $request, Building $building)
    {
        $data = $request->validate([
            'flat_number'      => ['required','string','max:50'],
            'flat_owner_name'  => ['nullable','string','max:120'],
            'flat_owner_phone' => ['nullable','string','max:30'],
        ]);

        Flat::create([
            'building_id'      => $building->id,
            'owner_id'         => auth()->id(),            // multi-tenant isolation
            'flat_number'      => $data['flat_number'],
            'flat_owner_name'  => $data['flat_owner_name'] ?? null,
            'flat_owner_phone' => $data['flat_owner_phone'] ?? null,
        ]);

        return redirect()
            ->route('owner.buildings.flats.index', $building)
            ->with('ok','Flat created');
    }

    // Edit/update/destroy (flat is auto-scoped by OwnerScope)
    public function edit(Flat $flat)  { return view('owner.flats.edit', compact('flat')); }

    public function update(Request $request, Flat $flat)
    {
        $data = $request->validate([
            'flat_number'      => ['required','string','max:50'],
            'flat_owner_name'  => ['nullable','string','max:120'],
            'flat_owner_phone' => ['nullable','string','max:30'],
        ]);
        $flat->update($data);
        return back()->with('ok','Flat updated');
    }

    public function destroy(Flat $flat)
    {
        $building = $flat->building;
        $flat->delete();
        return redirect()->route('owner.buildings.flats.index', $building)->with('ok','Flat deleted');
    }
}