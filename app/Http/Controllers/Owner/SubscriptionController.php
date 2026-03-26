<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function show(Request $request): View
    {
        $owner = $request->user();
        $subscription = $owner->ownerSubscription()->with('plan')->first();

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $starterPlan = SubscriptionPlan::defaultStarter()
            ?? $plans->firstWhere('is_free', true)
            ?? $plans->first();

        $starterSlug = $starterPlan?->slug ?? '';

        $currentPlan = $subscription?->plan;
        if ($currentPlan === null && $subscription?->subscription_plan_id && $plans->isNotEmpty()) {
            $currentPlan = $plans->firstWhere('id', $subscription->subscription_plan_id);
        }

        return view('owner.subscription.show', [
            'subscription' => $subscription,
            'plans' => $plans,
            'starterPlan' => $starterPlan,
            'currentPlan' => $currentPlan,
            'currentPlanId' => $subscription?->subscription_plan_id,
            'starterSlug' => $starterSlug,
            'defaultBillingSlug' => $starterSlug,
        ]);
    }
}
