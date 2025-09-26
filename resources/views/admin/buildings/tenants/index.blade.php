{{-- resources/views/admin/buildings/tenants/index.blade.php --}}
@extends('layouts.app')
@section('title','Building Tenants')

@section('content')
<h1 class="text-2xl font-semibold mb-2">Tenants for: {{ $building->name }}</h1>
<p class="text-gray-600 mb-4">
  Owner: <strong>{{ $building->owner?->name }}</strong> <span class="text-xs text-gray-500">{{ $building->owner?->email }}</span>
</p>

@if(session('ok')) <div class="mb-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div> @endif

<a href="{{ route('admin.buildings.tenants.create', $building) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Assign Tenant</a>

<div class="mt-4 bg-white rounded-lg shadow-sm border overflow-hidden">
  <table class="w-full text-left">
    <thead class="bg-gray-50 text-gray-600 text-sm">
      <tr>
        <th class="p-3">Tenant</th>
        <th class="p-3">Email</th>
        <th class="p-3">Phone</th>
        <th class="p-3">Start</th>
        <th class="p-3">End</th>
        <th class="p-3 w-32">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($tenants as $t)
      <tr>
        <td class="p-3">{{ $t->name }}</td>
        <td class="p-3">{{ $t->email }}</td>
        <td class="p-3">{{ $t->phone }}</td>
        <td class="p-3">{{ $t->pivot->start_date }}</td>
        <td class="p-3">{{ $t->pivot->end_date }}</td>
        <td class="p-3">
          <form method="POST" action="{{ route('admin.buildings.tenants.destroy', [$building->id, $t->id]) }}"
                onsubmit="return confirm('Remove this tenant from building?');">
            @csrf @method('DELETE')
            <button class="text-red-600">Remove</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td class="p-3 text-gray-500" colspan="6">No tenants assigned yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $tenants->links() }}</div>
@endsection
