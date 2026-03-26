<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerSubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    /**
     * Mark owner as paid for N months (manual billing until a gateway is integrated).
     */
    public function activatePaid(Request $request, User $owner): RedirectResponse
    {
        abort_unless($owner->role === 'owner', 404);

        $validated = $request->validate([
            'months' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $this->subscriptionService->activatePaidPeriod(
            $owner,
            now()->addMonths($validated['months'])
        );

        return back()->with('ok', __('Paid access granted for :months month(s).', ['months' => $validated['months']]));
    }

    /**
     * Extend free trial by N days (e.g. goodwill).
     */
    public function extendTrial(Request $request, User $owner): RedirectResponse
    {
        abort_unless($owner->role === 'owner', 404);

        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $sub = $owner->ownerSubscription;

        if (! $sub) {
            return back()->with('error', __('No subscription record for this owner.'));
        }

        $base = $sub->trial_ends_at && $sub->trial_ends_at->isFuture()
            ? $sub->trial_ends_at
            : now();

        $sub->update([
            'status' => \App\Models\OwnerSubscription::STATUS_TRIALING,
            'trial_ends_at' => $base->copy()->addDays($validated['days']),
            'current_period_start' => null,
            'current_period_end' => null,
        ]);

        return back()->with('ok', __('Trial extended by :days days.', ['days' => $validated['days']]));
    }
}
