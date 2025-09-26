@extends('layouts.app')
@section('title','Assign Tenant to Flat')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Assign: {{ $tenant->name }}</h1>
<p class="text-gray-600 mb-4">Building: <strong>{{ $building->name }}</strong></p>

<form method="POST" action="{{ route('owner.buildings.tenants.assign.store', [$building->id, $tenant->id]) }}"
      class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Select Flat</span>
    <select name="flat_id" class="mt-1 w-full border p-2 rounded" required>
      @foreach($flats as $f)
        <option value="{{ $f->id }}">Flat {{ $f->flat_number }}</option>
      @endforeach
    </select>
    @error('flat_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="text-sm text-gray-700">Start date</span>
      <input type="date" name="start_date" class="mt-1 w-full border p-2 rounded" required>
      @error('start_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
    <label class="block">
      <span class="text-sm text-gray-700">End date (optional)</span>
      <input type="date" name="end_date" class="mt-1 w-full border p-2 rounded">
      @error('end_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
  </div>

  <div class="mt-5 flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Assign</button>
    <a href="{{ route('owner.buildings.tenants.index', $building) }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
