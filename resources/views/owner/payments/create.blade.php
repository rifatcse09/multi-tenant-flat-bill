@extends('layouts.app')
@section('title','Add Payment')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Add Payment</h1>

<form method="POST" action="{{ route('owner.payments.store') }}"
      class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf

  {{-- Bill selector --}}
  <label class="block mb-3">
    <span class="text-sm text-gray-700">Bill</span>
    <select name="bill_id" class="mt-1 w-full border p-2 rounded" required>
      @if($bill)
        <option value="{{ $bill->id }}">
          {{ \Carbon\Carbon::parse($bill->month)->format('M Y') }} —
          Flat {{ $bill->flat?->flat_number }} —
          {{ $bill->category?->name }} —
          {{ ucfirst($bill->status) }}
        </option>
      @else
        <option value="">-- Select Bill --</option>
        @foreach($bills as $b)
          <option value="{{ $b->id }}">
            {{ \Carbon\Carbon::parse($b->month)->format('M Y') }} — Flat {{ $b->flat?->flat_number }} — {{ $b->category?->name }} — {{ ucfirst($b->status) }}
          </option>
        @endforeach
      @endif
    </select>
    @error('bill_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  {{-- Amount --}}
  <label class="block mb-3">
    <span class="text-sm text-gray-700">Amount</span>
    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 w-full border p-2 rounded" required>
    @error('amount')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  {{-- Paid at --}}
  <label class="block mb-3">
    <span class="text-sm text-gray-700">Paid at</span>
    <input type="datetime-local" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}"
           class="mt-1 w-full border p-2 rounded" required>
    @error('paid_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  {{-- Method & Reference --}}
  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="text-sm text-gray-700">Method (optional)</span>
      <input name="method" value="{{ old('method') }}" class="mt-1 w-full border p-2 rounded" placeholder="Cash / Bank / bKash / Card">
      @error('method')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
    <label class="block">
      <span class="text-sm text-gray-700">Reference (optional)</span>
      <input name="reference" value="{{ old('reference') }}" class="mt-1 w-full border p-2 rounded" placeholder="Txn ID / Slip No">
      @error('reference')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
  </div>

  <div class="mt-5 flex gap-2">
    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Save Payment</button>
    <a href="{{ route('owner.bills.index') }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
