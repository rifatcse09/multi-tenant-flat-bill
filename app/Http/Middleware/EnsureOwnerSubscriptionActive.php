<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerSubscriptionActive
{
    /**
     * House owners need an active trial or paid subscription (admins bypass).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isAdmin()) {
            return $next($request);
        }

        if (! $user->isOwner()) {
            return $next($request);
        }

        if ($request->routeIs('owner.subscription.*')) {
            return $next($request);
        }

        if ($user->hasActiveSubscriptionAccess()) {
            return $next($request);
        }

        return redirect()
            ->route('owner.subscription.show')
            ->with('error', __('Your free trial has ended. Please subscribe to continue using the platform.'));
    }
}
