<?php

use App\Models\OwnerSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_plans') || ! Schema::hasTable('owner_subscriptions')) {
            return;
        }

        $trialDays = (int) config('subscription.trial_months', 3) * 30;

        $plan = SubscriptionPlan::firstOrCreate(
            ['slug' => 'standard'],
            [
                'name' => 'Standard',
                'description' => 'Full access for property and billing management.',
                'price_monthly_cents' => 4999,
                'currency' => 'USD',
                'trial_days' => $trialDays,
                'is_active' => true,
                'sort_order' => 10,
            ]
        );

        $months = (int) config('subscription.trial_months', 3);

        User::query()
            ->where('role', 'owner')
            ->whereDoesntHave('ownerSubscription')
            ->each(function (User $owner) use ($plan, $months) {
                $trialEnds = ($owner->created_at ?? now())->copy()->addMonths($months);

                OwnerSubscription::create([
                    'user_id' => $owner->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => OwnerSubscription::STATUS_TRIALING,
                    'trial_ends_at' => $trialEnds,
                    'current_period_start' => null,
                    'current_period_end' => null,
                ]);
            });
    }

    public function down(): void
    {
        //
    }
};
