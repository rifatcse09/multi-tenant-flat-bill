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
@endsection
