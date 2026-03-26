<?php

namespace App\Services;

use App\Models\OwnerSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionService
{
    public function defaultPlan(): ?SubscriptionPlan
    {
        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->where('slug', 'free-15d')
            ->first()
            ?? SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->first();
    }

    /**
     * Create a free-trial subscription for a new owner (default: 15 days, see config).
     */
    public function createTrialForOwner(User $owner): OwnerSubscription
    {
        $plan = $this->defaultPlan();

        if (! $plan) {
            throw new \RuntimeException('No active subscription plan. Run SubscriptionPlanSeeder.');
        }

        $days = (int) config('subscription.trial_days', 15);

        return OwnerSubscription::create([
            'user_id' => $owner->id,
            'subscription_plan_id' => $plan->id,
            'status' => OwnerSubscription::STATUS_TRIALING,
            'trial_ends_at' => now()->addDays($days),
            'current_period_start' => null,
            'current_period_end' => null,
        ]);
    }

    /**
     * Ensure an owner has a subscription row (for migrated data).
     */
    public function ensureTrialForOwner(User $owner): OwnerSubscription
    {
        $existing = OwnerSubscription::where('user_id', $owner->id)->first();

        if ($existing) {
            return $existing;
        }

        return $this->createTrialForOwner($owner);
    }

    /**
     * Activate paid period after payment (manual admin or future gateway).
     */
    public function activatePaidPeriod(User $owner, ?\DateTimeInterface $until = null): OwnerSubscription
    {
        $sub = OwnerSubscription::where('user_id', $owner->id)->firstOrFail();
        $until = $until ?? now()->addMonth();

        $sub->update([
            'status' => OwnerSubscription::STATUS_ACTIVE,
            'current_period_start' => now(),
            'current_period_end' => $until,
            'canceled_at' => null,
        ]);

        return $sub->fresh();
    }
}
