<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 2) as $i) {
            User::updateOrCreate(
                ['email' => "owner{$i}@example.com"],
                [
                    'name' => "Owner {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'slug' => "owner{$i}",
                ]
            );
        }
    }
}