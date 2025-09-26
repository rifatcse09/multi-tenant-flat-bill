@extends('layouts.app')
@section('title','Building Tenants')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Tenants for: {{ $building->name }}</h1>
<p class="text-gray-600 mb-4">{{ $building->address }}</p>

@if(session('ok')) <div class="mb-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div> @endif

<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
  <table class="w-full text-left">
    <thead class="bg-gray-50 text-gray-600 text-sm">
      <tr>
        <th class="p-3">Tenant</th>
        <th class="p-3">Contact</th>
        <th class="p-3">Approved Period</th>
        <th class="p-3">Current Flat</th>
        <th class="p-3 w-48">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($tenants as $t)
        @php
          // find the current flat for this tenant within this building (if any)
          $currentFlat = $t->flats
            ->where('building_id', $building->id)
            ->filter(function ($f) {
              $sd = $f->pivot->start_date;
              $ed = $f->pivot->end_date;
              $today = now()->toDateString();
              return (!$sd || $sd <= $today) && (!$ed || $ed >= $today);
            })
            ->sortByDesc(fn($f) => $f->pivot->start_date)
            ->first();
        @endphp
        <tr>
          <td class="p-3 font-medium">{{ $t->name }}</td>
          <td class="p-3 text-sm">
            <div>{{ $t->email }}</div>
            <div class="text-gray-500">{{ $t->phone }}</div>
          </td>
          <td class="p-3 text-sm">
            {{ $t->pivot->start_date ?? '—' }} → {{ $t->pivot->end_date ?? 'present' }}
          </td>
          <td class="p-3">
            <a href="{{ route('owner.buildings.tenants.occupancies.index', [$building->id, $t->id]) }}" class="text-indigo-700">
                View Assignments</a>
          </td>
          <td class="p-3">

            {{-- Optional shortcuts --}}
            <a href="{{ route('owner.bills.index', ['tenant_id' => $t->id, 'building_id' => $building->id]) }}"
               class="text-blue-700 mr-3">View Bills</a>
          </td>
        </tr>
      @empty
        <tr><td class="p-3 text-gray-500" colspan="5">No tenants assigned by Admin yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
