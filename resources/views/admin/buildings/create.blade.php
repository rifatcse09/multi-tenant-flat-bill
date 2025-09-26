@extends('layouts.app')
@section('title','New Building')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Create Building</h1>

<form method="POST" action="{{ route('admin.buildings.store') }}" class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf
  <label class="block mb-3">
    <span class="text-sm text-gray-700">Owner</span>
    <select name="owner_id" class="mt-1 w-full border p-2 rounded">
      @foreach($owners as $o)
        <option value="{{ $o->id }}">{{ $o->name }} — {{ $o->email }}</option>
      @endforeach
    </select>
    @error('owner_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Name</span>
    <input name="name" value="{{ old('name') }}" class="mt-1 w-full border p-2 rounded">
    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-5">
    <span class="text-sm text-gray-700">Address (optional)</span>
    <input name="address" value="{{ old('address') }}" class="mt-1 w-full border p-2 rounded">
    @error('address')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
    <a href="{{ route('admin.buildings.index') }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
