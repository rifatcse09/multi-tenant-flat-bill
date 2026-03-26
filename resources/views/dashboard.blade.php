@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
  <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="p-2.5 rounded-lg bg-brand-50 text-brand-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <div>
        <div class="text-sm font-medium text-slate-500">Buildings</div>
        <div class="text-2xl font-bold text-slate-800">{{ number_format($totalBuildings ?? 0) }}</div>
      </div>
    </div>
  </div>
  <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="p-2.5 rounded-lg bg-brand-50 text-brand-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-10 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      </div>
      <div>
        <div class="text-sm font-medium text-slate-500">Flats</div>
        <div class="text-2xl font-bold text-slate-800">{{ number_format($totalFlats ?? 0) }}</div>
      </div>
    </div>
  </div>
  <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="p-2.5 rounded-lg bg-amber-50 text-amber-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <div>
        <div class="text-sm font-medium text-slate-500">Unpaid Bills</div>
        <div class="text-2xl font-bold text-slate-800">{{ number_format($unpaidBills ?? 0) }}</div>
      </div>
    </div>
  </div>
  <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <div class="text-sm font-medium text-slate-500">Payments (This Month)</div>
        <div class="text-2xl font-bold text-slate-800">{{ number_format($paymentsThisMonth ?? 0, 2) }}</div>
      </div>
    </div>
  </div>
</div>

@if(isset($adminAnalytics))
  <div
    id="adminDashboardAnalytics"
    class="mt-10 space-y-8"
    data-payment-labels='@json($adminAnalytics['paymentTrend']['labels'])'
    data-payment-values='@json($adminAnalytics['paymentTrend']['amounts'])'
    data-plan-labels='@json($adminAnalytics['subscriptionByPlan']['labels'])'
    data-plan-values='@json($adminAnalytics['subscriptionByPlan']['counts'])'
    data-status-labels='@json($adminAnalytics['subscriptionByStatus']['labels'])'
    data-status-values='@json($adminAnalytics['subscriptionByStatus']['counts'])'
    data-pl-labels='@json($adminAnalytics['profitLoss']['labels'])'
    data-pl-values='@json($adminAnalytics['profitLoss']['values'])'
  >
    <div class="border-b border-slate-200 pb-2">
      <h2 class="text-lg font-semibold text-slate-900">Analytics</h2>
      <p class="text-sm text-slate-600">Tenant payments, subscription mix, and receivables (admin view).</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payments (12 months)</div>
        <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($adminAnalytics['kpi']['paymentsLast12Months'], 2) }}</div>
        <div class="mt-1 text-xs text-slate-500">Sum of all recorded tenant payments</div>
      </div>
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Outstanding bills</div>
        <div class="mt-1 text-2xl font-bold text-red-700">${{ number_format($adminAnalytics['kpi']['outstandingBills'], 2) }}</div>
        <div class="mt-1 text-xs text-slate-500">Unpaid / partial — still owed</div>
      </div>
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Net position</div>
        <div class="mt-1 text-2xl font-bold {{ $adminAnalytics['kpi']['netPosition'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
          ${{ number_format($adminAnalytics['kpi']['netPosition'], 2) }}
        </div>
        <div class="mt-1 text-xs text-slate-500">12-mo collected minus outstanding</div>
      </div>
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Est. subscription MRR</div>
        <div class="mt-1 text-2xl font-bold text-brand-700">${{ number_format($adminAnalytics['kpi']['estimatedSubscriptionMrr'], 2) }}</div>
        <div class="mt-1 text-xs text-slate-500">{{ $adminAnalytics['kpi']['trialingSubscriptions'] }} trialing · {{ $adminAnalytics['kpi']['activePaidSubscriptions'] }} active paid</div>
      </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-800">Tenant payments over time</h3>
        <p class="mt-0.5 text-xs text-slate-500">Monthly totals (all owners)</p>
        <div class="mt-4 h-72">
          <canvas id="chartAdminPayments"></canvas>
        </div>
      </div>
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-800">Subscriptions by plan</h3>
        <p class="mt-0.5 text-xs text-slate-500">Owner accounts per plan</p>
        <div class="mt-4 h-72">
          <canvas id="chartAdminPlans"></canvas>
        </div>
      </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-800">Subscriptions by status</h3>
        <p class="mt-0.5 text-xs text-slate-500">Trial, active, expired, etc.</p>
        <div class="mt-4 h-64">
          <canvas id="chartAdminStatus"></canvas>
        </div>
      </div>
      <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-800">Collections vs receivables</h3>
        <p class="mt-0.5 text-xs text-slate-500">Collected (12 mo), outstanding balance, and net (simplified)</p>
        <div class="mt-4 h-64">
          <canvas id="chartAdminProfitLoss"></canvas>
        </div>
      </div>
    </div>
  </div>
@endif
@endsection

@if(isset($adminAnalytics))
  @push('scripts')
    @vite(['resources/js/admin-dashboard.js'])
  @endpush
@endif
