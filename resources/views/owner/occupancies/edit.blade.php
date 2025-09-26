@extends('layouts.app')
@section('title','Edit Assignment')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Edit Assignment</h1>
<p class="text-gray-600 mb-4">{{ $tenant->name }} — {{ $building->name }}</p>

<form method="POST" action="{{ route('owner.buildings.tenants.occupancies.update', [$building->id, $tenant->id, $row->id]) }}"
      class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf @method('PUT')

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Flat</span>
    <select name="flat_id" class="mt-1 w-full border p-2 rounded" required>
      @foreach($flats as $f)
      <option value="{{ $f->id }}" @selected($f->id == $row->flat_id)>Flat {{ $f->flat_number }}</option>
      @endforeach
    </select>
  </label>

  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="text-sm text-gray-700">Start date</span>
      <input type="date" name="start_date" value="{{ $row->start_date }}" class="mt-1 w-full border p-2 rounded" required>
    </label>
    <label class="block">
      <span class="text-sm text-gray-700">End date (optional)</span>
      <input type="date" name="end_date" value="{{ $row->end_date }}" class="mt-1 w-full border p-2 rounded">
    </label>
  </div>

  <div class="mt-5 flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
    <a href="{{ route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id]) }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
