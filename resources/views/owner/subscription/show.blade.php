@extends('layouts.app')
@section('title', 'Subscription')

@section('content')
    @php
        $alpineState = \Illuminate\Support\Js::from([
            'step' => 'plans',
            'billing' => $defaultBillingSlug,
            'starterSlug' => $starterSlug,
            'planNames' => $plans->mapWithKeys(fn ($p) => [$p->slug => $p->name])->all(),
        ]);
    @endphp

    <div
        class="subscription-pricing w-full rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/90 px-4 py-8 shadow-sm ring-1 ring-slate-200/60 md:px-8 md:py-10"
        x-data="{{ $alpineState }}"
    >
        <div class="mx-auto max-w-6xl space-y-8 md:space-y-10">
            <div class="text-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Choose your <span class="text-brand-600">plan</span>
                </h1>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600">
                    @if ($starterPlan)
                        Everyone starts on <strong class="font-semibold text-slate-800">{{ $starterPlan->name }}</strong>
                        @if ($starterPlan->is_free)
                            (free trial)
                        @endif
                        . To upgrade, pick another plan — you’ll enter card details on the next step.
                    @else
                        Choose a plan below. Paid options use card details on the next step.
                    @endif
                </p>
            </div>

            @if (session('error'))
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm text-amber-900">
                    {{ session('error') }}
                </div>
            @endif

            @if ($subscription)
                @php
                    $hasAccess = $subscription->grantsAccess();
                @endphp

                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-brand-50/60 px-5 py-4">
                        <h2 class="font-semibold text-slate-900">Account status</h2>
                        <p class="mt-0.5 text-sm text-slate-600">{{ $currentPlan?->name ?? '—' }} ·
                            <span class="font-medium capitalize text-brand-700">{{ str_replace('_', ' ', $subscription->status) }}</span>
                        </p>
                    </div>
                    <div class="grid gap-4 p-5 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        @if ($subscription->isTrialing())
                            <div>
                                <div class="text-slate-500">Trial ends</div>
                                <div class="font-semibold text-slate-900">{{ $subscription->trial_ends_at->format('M j, Y') }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Days remaining</div>
                                <div class="font-semibold text-slate-900">{{ $subscription->trialDaysRemaining() ?? 0 }}</div>
                            </div>
                        @endif
                        @if ($subscription->status === \App\Models\OwnerSubscription::STATUS_ACTIVE && $subscription->current_period_end)
                            <div>
                                <div class="text-slate-500">Paid access until</div>
                                <div class="font-semibold text-slate-900">{{ $subscription->current_period_end->format('M j, Y') }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if (! $hasAccess)
                    <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-red-900">
                        <h3 class="font-semibold">Access paused</h3>
                        <p class="mt-2 text-sm leading-relaxed text-red-800">
                            Your free trial has ended. Pick a plan below or contact your administrator to activate billing.
                        </p>
                    </div>
                @endif
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm text-amber-950">
                    No subscription record on file. Plans below show what we offer — contact support to link your account.
                </div>
            @endif

            <section aria-labelledby="subscription-plans-heading">
                <h2 id="subscription-plans-heading" class="sr-only">Pricing plans</h2>

                @if ($plans->isEmpty())
                    <p class="text-center text-sm text-slate-600">No plans configured.</p>
                @else
                    @php
                        $currentPlanSlug = $currentPlan?->slug;
                        $emphasizeSlug = $currentPlanSlug ?? $starterSlug;
                        $currentPlanName = $currentPlan?->name;
                    @endphp
                    {{-- Step 1: ring from DB current plan; default highlight from DB default free (starter) plan --}}
                    <div x-show="step === 'plans'" x-transition.opacity.duration.200ms>
                        <div
                            class="mx-auto mb-8 max-w-3xl rounded-xl border border-slate-200/80 bg-slate-50/90 px-5 py-4 text-center sm:px-8 sm:py-5 md:mb-10"
                        >
                            <p class="text-sm leading-relaxed text-slate-600 sm:text-base sm:leading-relaxed">
                                <span class="font-medium text-slate-800">Step 1 of 2</span>
                                <span class="text-slate-400"> — </span>
                                @if ($currentPlanName)
                                    <strong class="font-semibold text-slate-800">{{ $currentPlanName }}</strong> is your current plan (highlighted below).
                                @elseif ($starterPlan)
                                    <strong class="font-semibold text-slate-800">{{ $starterPlan->name }}</strong> is the default; choose a paid plan to continue to payment.
                                @else
                                    Choose a plan below.
                                @endif
                            </p>
                        </div>
                        <div class="grid grid-cols-1 gap-8 px-2 py-2 sm:px-4 sm:py-4 md:grid-cols-3 md:gap-6 lg:gap-8 lg:px-6">
                            @foreach ($plans as $planCard)
                                @php
                                    $isCurrent = isset($currentPlanId) && (int) $currentPlanId === (int) $planCard->id;
                                @endphp
                                <div
                                    class="min-w-0 rounded-2xl p-1 transition-all duration-200 sm:p-1.5"
                                    x-bind:class="step === 'plans' && '{{ $planCard->slug }}' === '{{ $emphasizeSlug }}'
                                        ? 'ring-2 ring-brand-500 ring-offset-2 ring-offset-slate-50 md:scale-[1.01]'
                                        : ''"
                                >
                                    <x-subscription-plan-card
                                        :plan="$planCard"
                                        :is-current="$isCurrent"
                                        :interactive-subscribe="true"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 2: card payment (only after Quarterly / Yearly chosen on a card) --}}
                    <div
                        x-cloak
                        x-show="step === 'checkout'"
                        x-transition.opacity.duration.200ms
                        class="mx-auto mt-8 max-w-xl rounded-2xl border border-slate-200 bg-white px-6 pb-8 pt-10 shadow-sm sm:mt-10 sm:px-8 sm:pb-10 sm:pt-12"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Step 2 of 2</p>
                                <h3 class="mt-1 text-lg font-semibold text-slate-900">
                                    Card payment — <span class="text-brand-600" x-text="planNames[billing] || ''"></span>
                                </h3>
                            </div>
                            <button
                                type="button"
                                class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                x-on:click="step = 'plans'; billing = starterSlug"
                            >
                                Back
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">
                            Enter card details below. Live charging can be wired to Stripe or your gateway later.
                        </p>

                        <form class="mt-6 space-y-5" @submit.prevent="window.alert('Subscription checkout is not connected yet. Ask your admin to activate billing, or integrate Stripe.');">
                            <fieldset>
                                <legend class="text-sm font-medium text-slate-700">Payment method</legend>
                                <div class="mt-3 space-y-2">
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 bg-brand-50/50 px-4 py-3 ring-2 ring-brand-500">
                                        <input type="radio" name="pay_method" value="card" checked class="text-brand-600 focus:ring-brand-500">
                                        <span class="text-sm font-medium text-slate-900">Credit or debit card</span>
                                    </label>
                                </div>
                            </fieldset>

                            <div class="space-y-4">
                                <div>
                                    <label for="card_name" class="block text-sm font-medium text-slate-700">Name on card</label>
                                    <input
                                        id="card_name"
                                        type="text"
                                        name="card_name"
                                        autocomplete="cc-name"
                                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                        placeholder="Jane Owner"
                                    >
                                </div>
                                <div>
                                    <label for="card_number" class="block text-sm font-medium text-slate-700">Card number</label>
                                    <input
                                        id="card_number"
                                        type="text"
                                        name="card_number"
                                        inputmode="numeric"
                                        autocomplete="cc-number"
                                        maxlength="19"
                                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                        placeholder="4242 4242 4242 4242"
                                    >
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="card_exp" class="block text-sm font-medium text-slate-700">Expiry</label>
                                        <input
                                            id="card_exp"
                                            type="text"
                                            name="card_exp"
                                            autocomplete="cc-exp"
                                            maxlength="5"
                                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                            placeholder="MM / YY"
                                        >
                                    </div>
                                    <div>
                                        <label for="card_cvc" class="block text-sm font-medium text-slate-700">CVC</label>
                                        <input
                                            id="card_cvc"
                                            type="text"
                                            name="card_cvc"
                                            autocomplete="cc-csc"
                                            maxlength="4"
                                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                            placeholder="123"
                                        >
                                    </div>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                            >
                                Pay and subscribe
                            </button>
                        </form>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
