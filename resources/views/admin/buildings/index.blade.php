@extends('layouts.app')
@section('title','Buildings')

@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-2xl font-semibold">Buildings</h1>
  <a href="{{ route('admin.buildings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Building</a>
</div>

<form method="GET" class="mt-4">
  <div class="flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Search name/address" class="border p-2 rounded w-full">
    <button class="px-4 py-2 border rounded">Search</button>
  </div>
</form>

@if(session('ok')) <div class="mt-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div> @endif

<div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
  <table class="w-full text-left">
    <thead class="bg-gray-50 text-gray-600 text-sm">
      <tr>
        <th class="p-3">Name</th>
        <th class="p-3">Address</th>
        <th class="p-3">Owner</th>
        <th class="p-3 w-40">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($buildings as $b)
      <tr>
        <td class="p-3">{{ $b->name }}</td>
        <td class="p-3">{{ $b->address }}</td>
        <td class="p-3">{{ $b->owner?->name }} <span class="text-gray-500 text-xs">{{ $b->owner?->email }}</span></td>
        <td class="p-3">
            <a href="{{ route('admin.buildings.tenants.index', $b) }}" class="text-indigo-700 mr-3">
                Manage Tenants
            </a>
          <a href="{{ route('admin.buildings.edit',$b) }}" class="text-blue-700 mr-3">Edit</a>
          <form method="POST" action="{{ route('admin.buildings.destroy',$b) }}" class="inline" onsubmit="return confirm('Delete this building?');">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td class="p-3 text-gray-500" colspan="4">No buildings found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $buildings->links() }}</div>
@endsection
