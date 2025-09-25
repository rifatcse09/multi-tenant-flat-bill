<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Alice','Bob','Charlie','Diana','Evan'] as $name) {
        Tenant::updateOrCreate(
            ['email' => strtolower($name).'@example.com'],
            ['name' => $name, 'phone' => '017000000'.rand(1,9)]
            );
        }
    }
}