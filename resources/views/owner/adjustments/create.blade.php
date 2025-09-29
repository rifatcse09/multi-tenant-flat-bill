@extends('layouts.app')
@section('title','Add Due / Adjustment')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Add Due / Adjustment</h1>

<form method="POST" action="{{ route('owner.adjustments.store') }}" class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Bill</span>
    <select name="bill_id" class="mt-1 w-full border p-2 rounded" required>
      @if($bill)
        <option value="{{ $bill->id }}">
          {{ \Carbon\Carbon::parse($bill->month)->format('M Y') }} — Flat {{ $bill->flat?->flat_number }} — {{ $bill->category?->name }} — Due: {{ number_format($bill->due,2) }}
        </option>
      @else
        <option value="">-- Select Bill --</option>
        @foreach($bills as $b)
          <option value="{{ $b->id }}">
            {{ \Carbon\Carbon::parse($b->month)->format('M Y') }} — Flat {{ $b->flat?->flat_number }} — {{ $b->category?->name }} — Due: {{ number_format($b->due,2) }}
          </option>
        @endforeach
      @endif
    </select>
    @error('bill_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Amount</span>
    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 w-full border p-2 rounded" required>
    <div class="text-xs text-gray-500 mt-1">Positive = add due; Negative = discount/waiver.</div>
    @error('amount')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="text-sm text-gray-700">Type (optional)</span>
      <input name="type" value="{{ old('type','manual_due') }}" class="mt-1 w-full border p-2 rounded">
      @error('type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
    <label class="block">
      <span class="text-sm text-gray-700">Reason (optional)</span>
      <input name="reason" value="{{ old('reason') }}" class="mt-1 w-full border p-2 rounded">
      @error('reason')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
  </div>

  <div class="mt-5 flex gap-2">
    <button class="bg-amber-600 text-white px-4 py-2 rounded">Add Adjustment</button>
    <a href="{{ route('owner.bills.index') }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
