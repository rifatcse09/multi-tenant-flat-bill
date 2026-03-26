<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptionService = app(SubscriptionService::class);

        foreach (range(1, 2) as $i) {
            $owner = User::updateOrCreate(
                ['email' => "owner{$i}@example.com"],
                [
                    'name' => "Owner {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'slug' => "owner{$i}",
                ]
            );

            $subscriptionService->ensureTrialForOwner($owner);
        }
    }
}