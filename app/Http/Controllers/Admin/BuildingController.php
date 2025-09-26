<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\User;
use Illuminate\Http\Request;

class BuildingController extends Controller {

    public function index(Request $request)
    {
        $q=trim($request->get('q',''));

        $buildings = Building::withoutGlobalScopes()
        ->with('owner')
        ->when($q, fn($qry) => $qry->where(function($s) use ($q){
        $s->where('name','like',"%$q%")
        ->orWhere('address','like',"%$q%");
        }))
        ->latest()
        ->paginate(12)
        ->withQueryString();

        return view('admin.buildings.index', compact('buildings','q'));
    }

    public function create()
    {
        $owners = User::where('role','owner')->orderBy('name')->get(['id','name','email']);
        return view('admin.buildings.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
        'owner_id' => ['required','exists:users,id'],
        'name' => ['required','string','max:120'],
        'address' => ['nullable','string','max:255'],
        ]);

        Building::withoutGlobalScopes()->create($data);

        return redirect()->route('admin.buildings.index')->with('ok','Building created');
    }

    public function edit(Building $building)
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);
        $owners = User::where('role','owner')->orderBy('name')->get(['id','name','email']);
        return view('admin.buildings.edit', compact('building','owners'));
    }

    public function update(Request $request, Building $building)
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);

        $data = $request->validate([
        'owner_id' => ['required','exists:users,id'],
        'name' => ['required','string','max:120'],
        'address' => ['nullable','string','max:255'],
        ]);

        $building->update($data);

        return redirect()->route('admin.buildings.index')->with('ok','Building updated');
    }

    public function destroy(Building $building)
    {
        $building = Building::withoutGlobalScopes()->findOrFail($building->id);
        $building->delete();
        return redirect()->route('admin.buildings.index')->with('ok','Building deleted');
    }
}