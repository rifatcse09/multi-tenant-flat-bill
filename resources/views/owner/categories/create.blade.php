@extends('layouts.app')
@section('title','New Category')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Create Category</h1>

<form method="POST" action="{{ route('owner.categories.store') }}"
      class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf

  <label class="block mb-5">
    <span class="text-sm text-gray-700">Name</span>
    <input name="name" value="{{ old('name') }}" class="mt-1 w-full border p-2 rounded" required>
    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
    <a href="{{ route('owner.categories.index') }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
