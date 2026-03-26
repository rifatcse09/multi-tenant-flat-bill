<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $trialDays = (int) config('subscription.trial_days', 15);

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'free-15d'],
            [
                'name' => 'Starter',
                'description' => 'Try every owner feature free — buildings, flats, bills, and payments. No card required.',
                'price_monthly_cents' => 0,
                'price_total_cents' => 0,
                'currency' => 'USD',
                'trial_days' => $trialDays,
                'billing_period_months' => null,
                'is_free' => true,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 10,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'quarterly'],
            [
                'name' => 'Quarterly',
                'description' => 'Full platform access. Best for steady portfolios — billed every three months.',
                'price_monthly_cents' => 4300,
                'price_total_cents' => 12900,
                'currency' => 'USD',
                'trial_days' => 0,
                'billing_period_months' => 3,
                'is_free' => false,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 20,
            ]
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'yearly'],
            [
                'name' => 'Yearly',
                'description' => 'Same features as Quarterly with the best long-term rate — one annual payment.',
                'price_monthly_cents' => 3325,
                'price_total_cents' => 39900,
                'currency' => 'USD',
                'trial_days' => 0,
                'billing_period_months' => 12,
                'is_free' => false,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 30,
            ]
        );

        SubscriptionPlan::whereIn('slug', ['standard', 'professional'])->update(['is_active' => false]);
    }
}
