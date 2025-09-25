<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Building;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::where('role','owner')->get() as $owner) {
            Building::firstOrCreate(
                ['owner_id' => $owner->id, 'name' => "{$owner->name}'s Building"],
                ['address' => '123 Demo Street']
            );
        }
    }
}