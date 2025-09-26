@extends('layouts.app')
@section('title','New Flat to '.$building->name)

@section('content')
<h1 class="text-2xl font-semibold mb-4">Create Flat to {{ $building->name }}</h1>

<form method="POST" action="{{ route('owner.buildings.flats.store',$building) }}"
      class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf
  <label class="block mb-3">
    <span class="text-sm text-gray-700">Flat number</span>
    <input name="flat_number" value="{{ old('flat_number') }}" class="mt-1 w-full border p-2 rounded" required>
    @error('flat_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="grid grid-cols-2 gap-3">
    <label class="block">
      <span class="text-sm text-gray-700">Flat owner (optional)</span>
      <input name="flat_owner_name" value="{{ old('flat_owner_name') }}" class="mt-1 w-full border p-2 rounded">
      @error('flat_owner_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
    <label class="block">
      <span class="text-sm text-gray-700">Phone (optional)</span>
      <input name="flat_owner_phone" value="{{ old('flat_owner_phone') }}" class="mt-1 w-full border p-2 rounded">
      @error('flat_owner_phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </label>
  </div>

  <div class="mt-5 flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
    <a href="{{ route('owner.buildings.flats.index',$building) }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
