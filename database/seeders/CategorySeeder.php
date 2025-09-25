<?php

// database/seeders/CategorySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BillCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = ['Electricity','Gas','Water','Service Charge'];
        foreach (User::where('role','owner')->get() as $owner) {
            foreach ($defaults as $name) {
                BillCategory::firstOrCreate(['owner_id'=>$owner->id,'name'=>$name]);
            }
        }
    }
}