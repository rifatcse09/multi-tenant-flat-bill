@extends('layouts.app')
@section('title','Edit Owner')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Owner</h1>

<form method="POST" action="{{ route('admin.owners.update', $owner) }}" class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
  @csrf @method('PUT')

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Name</span>
    <input name="name" value="{{ old('name', $owner->name) }}" class="mt-1 w-full border p-2 rounded">
    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Email</span>
    <input name="email" type="email" value="{{ old('email', $owner->email) }}" class="mt-1 w-full border p-2 rounded">
    @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-3">
    <span class="text-sm text-gray-700">Slug (subdomain) – optional</span>
    <input name="slug" value="{{ old('slug', $owner->slug) }}" class="mt-1 w-full border p-2 rounded">
    @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <label class="block mb-5">
    <span class="text-sm text-gray-700">New Password (optional)</span>
    <input name="password" type="password" class="mt-1 w-full border p-2 rounded" placeholder="leave empty to keep current">
    @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </label>

  <div class="flex gap-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
    <a href="{{ route('admin.owners.index') }}" class="px-4 py-2 border rounded">Cancel</a>
  </div>
</form>
@endsection
