@props([
    'plan',
    'isCurrent' => false,
    /** When true, paid plans get a button that opens the card checkout step (Alpine on parent). */
    'interactiveSubscribe' => false,
])

@php
    $featured = $plan->is_featured;
@endphp

<article
    {{ $attributes->class([
        'flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border shadow-sm transition-all duration-300',
        'border-brand-400 bg-brand-50/40 shadow-md' => $isCurrent,
        'border-brand-300 bg-gradient-to-b from-white to-brand-50/30 shadow-md ring-1 ring-brand-200/50' => ! $isCurrent && $featured,
        'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' => ! $isCurrent && ! $featured,
    ]) }}
    data-plan-slug="{{ $plan->slug }}"
    aria-label="{{ $plan->name }} subscription plan"
>
    {{-- Badge strip inside card — no negative margins, no overlap with border --}}
    <div
        class="flex min-h-[3rem] shrink-0 items-center justify-center border-b border-slate-100 bg-slate-50/90 px-5 py-3 text-center sm:px-6 sm:py-3.5">
        @if ($isCurrent)
            <span
                class="inline-flex items-center rounded-full bg-brand-600 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm">
                Current plan
            </span>
        @elseif ($plan->is_free)
            <span
                class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 text-[11px] font-semibold text-white">
                Free trial
            </span>
        @elseif ($featured)
            <span
                class="inline-flex items-center rounded-full bg-brand-600 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white shadow-sm">
                Most popular
            </span>
        @else
            <span
                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-600 shadow-sm">
                {{ $plan->displayPeriodBadge() }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col px-6 pb-8 pt-6 sm:px-8 sm:pb-10 sm:pt-7">
        <div class="text-center md:text-left">
            <h3 class="text-xl font-bold text-slate-900">{{ $plan->name }}</h3>
            <div class="mt-4">
                @if ($plan->is_free)
                    <p class="text-4xl font-bold tabular-nums tracking-tight text-slate-900">$0</p>
                    <p class="mt-1 text-sm text-slate-500">for the trial period</p>
                    <p class="mt-2 text-sm font-medium text-brand-700">{{ $plan->trial_days }} days full access</p>
                @else
                    <p class="text-4xl font-bold tabular-nums tracking-tight text-slate-900">{{ $plan->displayPricePrimary() }}</p>
                    <p class="mt-1 text-sm text-slate-500">per billing period</p>
                @endif
            </div>
            @php $priceSecondary = $plan->displayPriceSecondary(); @endphp
            @if ($priceSecondary && ! $plan->is_free)
                <p class="mt-3 text-sm font-semibold text-brand-600">{{ $priceSecondary }}</p>
            @endif
        </div>

        @if ($plan->description)
            <p class="mt-5 flex-1 text-center text-sm leading-relaxed text-slate-600 md:text-left">{{ $plan->description }}</p>
        @endif

        <ul class="mt-7 space-y-3 text-sm text-slate-700">
            <li class="flex gap-3 justify-center md:justify-start">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                Buildings, flats & tenants
            </li>
            <li class="flex gap-3 justify-center md:justify-start">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                Bills, payments & adjustments
            </li>
            <li class="flex gap-3 justify-center md:justify-start">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                Email notifications
            </li>
        </ul>

        <div class="mt-9 sm:mt-10">
            @if ($isCurrent)
                <span
                    class="flex w-full items-center justify-center rounded-xl border-2 border-brand-500 bg-brand-50 py-3 text-sm font-semibold text-brand-800">
                    Active on your account
                </span>
            @elseif ($interactiveSubscribe && $plan->is_free)
                <button
                    type="button"
                    disabled
                    class="w-full cursor-default rounded-xl border-2 border-brand-600 bg-brand-50 py-3 text-sm font-semibold text-brand-800 shadow-sm">
                    Default — {{ $plan->name }} trial
                </button>
                <p class="mt-3 text-center text-xs text-slate-500">No card required · Included for new accounts.</p>
            @elseif ($interactiveSubscribe && ! $plan->is_free)
                <button
                    type="button"
                    class="w-full rounded-xl py-3 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 {{ $featured ? 'bg-brand-600 text-white hover:bg-brand-700' : 'border-2 border-slate-300 bg-white text-slate-900 hover:border-brand-400 hover:bg-brand-50' }}"
                    x-on:click="billing = '{{ $plan->slug }}'; step = 'checkout'"
                >
                    Continue with card
                </button>
                <p class="mt-3 text-center text-xs text-slate-500">Next step: secure card payment.</p>
            @elseif ($plan->is_free)
                <button
                    type="button"
                    disabled
                    class="w-full cursor-not-allowed rounded-xl border-2 border-brand-600 bg-white py-3 text-sm font-semibold text-brand-700 shadow-sm">
                    Start free trial
                </button>
                <p class="mt-3 text-center text-xs text-slate-500">No card required · Contact admin if you need help.</p>
            @elseif ($featured)
                <button
                    type="button"
                    disabled
                    class="w-full cursor-not-allowed rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-md">
                    Subscribe now
                </button>
                <p class="mt-3 text-center text-xs text-slate-500">Contact admin to complete checkout.</p>
            @else
                <button
                    type="button"
                    disabled
                    class="w-full cursor-not-allowed rounded-xl border-2 border-slate-300 bg-white py-3 text-sm font-semibold text-slate-800">
                    Subscribe now
                </button>
                <p class="mt-3 text-center text-xs text-slate-500">Contact admin to activate.</p>
            @endif
        </div>
    </div>
</article>
