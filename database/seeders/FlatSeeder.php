<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Flat;

class FlatSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Building::all() as $building) {
            foreach (range(1,4) as $i) {
                Flat::firstOrCreate([
                    'building_id' => $building->id,
                    'owner_id' => $building->owner_id,
                    'flat_number' => "A-$i",
                ],[
                    'flat_owner_name' => "FlatOwner $i",
                    'flat_owner_phone'=> '018000000'.rand(1,9),
                ]);
            }
        }
    }
}