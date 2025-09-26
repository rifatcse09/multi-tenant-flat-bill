<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Building;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::withCount('flats')->latest()->paginate(12);
        return view('owner.buildings.index', compact('buildings'));
    }
}