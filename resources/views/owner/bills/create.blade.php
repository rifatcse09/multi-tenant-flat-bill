@extends('layouts.app')
@section('title','Create Bill')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Create Bill</h1>

<form method="POST" action="{{ route('owner.bills.store') }}" class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf
  <label class="block mb-3">
    <span class="text-sm text-gray-700">Flat</span>
    <select name="flat_id" class="mt-1 w-full border p-2 rounded" required>
      @foreach($flats as $f)
        <option value="{{ $f->id }}">Flat {{ $f->flat_number }}</option>
      @endforeach
    </select>
    @error('flat_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Category</span>
    <select name="bill_category_id" class="mt-1 w-full border p-2 rounded" required>
      @foreach($categories as $c)
        <option value="{{ $c->id }}">{{ $c->name }}</option>
      @endforeach
    </select>
    @error('bill_category_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="text-sm text-gray-700">Month</span>
      <input type="month" name="month" class="mt-1 w-full border p-2 rounded" required>
      @error('month')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
    <label class="block">
      <span class="text-sm text-gray-700">Amount</span>
      <input type="number" step="0.01" name="amount" class="mt-1 w-full border p-2 rounded" required>
      @error('amount')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
  </div>

  <label class="block mb-5">
    <span class="text-sm text-gray-700">Notes (optional)</span>
    <textarea name="notes" class="mt-1 w-full border p-2 rounded" rows="3"></textarea>
  </label>

  <div class="flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
    <a href="{{ route('owner.bills.index') }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
