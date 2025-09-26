@extends('layouts.app')
@section('title','Bills')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Bills</h1>

<form method="GET" class="bg-white rounded-lg shadow-sm border p-4 grid md:grid-cols-6 gap-3">
  <div>
    <label class="text-sm text-gray-600">Flat</label>
    <select name="flat_id" class="w-full border p-2 rounded">
      <option value="">All</option>
      @foreach($flats as $f)
        <option value="{{ $f->id }}" @selected($filters['flat_id']==$f->id)>Flat {{ $f->flat_number }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="text-sm text-gray-600">Category</label>
    <select name="category_id" class="w-full border p-2 rounded">
      <option value="">All</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected($filters['category_id']==$c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="text-sm text-gray-600">Status</label>
    <select name="status" class="w-full border p-2 rounded">
      <option value="">All</option>
      @foreach(['unpaid','partial','paid'] as $s)
        <option value="{{ $s }}" @selected($filters['status']===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="text-sm text-gray-600">Bill To</label>
    <select name="bill_to" class="w-full border p-2 rounded">
      <option value="">All</option>
      @foreach(['tenant','owner'] as $bt)
        <option value="{{ $bt }}" @selected($filters['bill_to']===$bt)>{{ ucfirst($bt) }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="text-sm text-gray-600">Month From</label>
    <input type="month" name="month_from" value="{{ $filters['month_from'] }}" class="w-full border p-2 rounded">
  </div>
  <div>
    <label class="text-sm text-gray-600">Month To</label>
    <input type="month" name="month_to" value="{{ $filters['month_to'] }}" class="w-full border p-2 rounded">
  </div>
  <div class="md:col-span-3">
    <label class="text-sm text-gray-600">Tenant (name/email)</label>
    <input name="q" value="{{ $filters['q'] }}" class="w-full border p-2 rounded" placeholder="Search tenant">
  </div>
  <div class="md:col-span-3 flex items-end gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    <a href="{{ route('owner.bills.index') }}" class="px-4 py-2 border rounded">Reset</a>
    <a href="{{ route('owner.bills.create') }}" class="ml-auto bg-green-600 text-white px-4 py-2 rounded">+ Create Bill</a>
  </div>
</form>

{{-- Totals --}}
<div class="mt-4 grid sm:grid-cols-4 gap-3">
  <div class="bg-white border rounded p-3">
    <div class="text-xs text-gray-500">Amount</div>
    <div class="text-xl font-semibold">{{ number_format($totals['amount'],2) }}</div>
  </div>
  <div class="bg-white border rounded p-3">
    <div class="text-xs text-gray-500">Carry Forward</div>
    <div class="text-xl font-semibold">{{ number_format($totals['carry'],2) }}</div>
  </div>
  <div class="bg-white border rounded p-3">
    <div class="text-xs text-gray-500">Paid</div>
    <div class="text-xl font-semibold">{{ number_format($totals['paid'],2) }}</div>
  </div>
  <div class="bg-white border rounded p-3">
    <div class="text-xs text-gray-500">Total Due</div>
    <div class="text-xl font-semibold">{{ number_format($totals['due'],2) }}</div>
  </div>
</div>

{{-- Table --}}
<div class="mt-4 bg-white rounded-lg shadow-sm border overflow-hidden">
  <table class="w-full text-left">
    <thead class="bg-gray-50 text-gray-600 text-sm">
      <tr>
        <th class="p-3">Month</th>
        <th class="p-3">Flat</th>
        <th class="p-3">Category</th>
        <th class="p-3">Bill To</th>
        <th class="p-3">Amount</th>
        <th class="p-3">Carry</th>
        <th class="p-3">Paid</th>
        <th class="p-3">Due</th>
        <th class="p-3">Status</th>
        <th class="p-3 w-44">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($bills as $b)
        @php
          $paid = $b->payments()->sum('amount');
          $due  = max(0, ($b->amount + $b->due_carry_forward) - $paid);
        @endphp
        <tr>
          <td class="p-3">{{ \Carbon\Carbon::parse($b->month)->format('M Y') }}</td>
          <td class="p-3">Flat {{ $b->flat?->flat_number }}</td>
          <td class="p-3">{{ $b->category?->name }}</td>
          <td class="p-3">
            @if($b->bill_to === 'tenant')
              Tenant — <span class="text-sm text-gray-700">{{ $b->tenant?->name ?? 'N/A' }}</span>
            @else Owner @endif
          </td>
          <td class="p-3">{{ number_format($b->amount,2) }}</td>
          <td class="p-3">{{ number_format($b->due_carry_forward,2) }}</td>
          <td class="p-3">{{ number_format($paid,2) }}</td>
          <td class="p-3">{{ number_format($due,2) }}</td>
          <td class="p-3">
            <span class="px-2 py-1 rounded text-xs
              @if($b->status==='paid') bg-green-100 text-green-700
              @elseif($b->status==='partial') bg-amber-100 text-amber-700
              @else bg-red-100 text-red-700 @endif">
              {{ ucfirst($b->status) }}
            </span>
          </td>
          <td class="p-3">

            <a href="{{ route('owner.payments.create', ['bill_id' => $b->id]) }}"
                class="text-indigo-700 mr-3">Add Payment</a>
            {{-- placeholders; wire your routes if you have show/pay --}}
            {{-- <a href="{{ route('owner.bills.edit', $b->id ?? 0) }}" class="text-blue-700 mr-3">Edit</a>
            <a href="{{ route('owner.payments.create', ['bill_id' => $b->id]) }}" class="text-indigo-700 mr-3">Add Payment</a>
            <form method="POST" action="{{ route('owner.bills.destroy', $b->id ?? 0) }}" class="inline"
                  onsubmit="return confirm('Delete this bill?');">
              @csrf @method('DELETE')
              <button class="text-red-600">Delete</button>
            </form> --}}
          </td>
        </tr>
      @empty
        <tr><td class="p-3 text-gray-500" colspan="10">No bills match your filters.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $bills->links() }}</div>
@endsection
