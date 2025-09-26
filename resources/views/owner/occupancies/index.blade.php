@extends('layouts.app')
@section('title', 'Tenant Occupancy')

@section('content')
    <h1 class="text-2xl font-semibold mb-2">
        {{ $tenant->name }} — {{ $building->name }}
    </h1>
    <p class="text-gray-600 mb-4">{{ $building->address }}</p>

    @if (session('ok'))
        <div class="mb-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div>
    @endif

    <div class="flex items-center gap-3">
        <a href="{{ route('owner.buildings.tenants.index', $building) }}" class="px-3 py-2 border rounded">Back to Tenants</a>
        <a href="{{ route('owner.buildings.tenants.occupancies.create', [$building->id, $tenant->id]) }}"
            class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Assignment</a>
    </div>

    <div class="mt-4 bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm">
                <tr>
                    <th class="p-3">Flat</th>
                    <th class="p-3">Start</th>
                    <th class="p-3">End</th>
                    <th class="p-3 w-64">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($occupancies as $o)
                    <tr>
                        <td class="p-3">Flat {{ $o->flat_number }}</td>
                        <td class="p-3">{{ $o->start_date }}</td>
                        <td class="p-3">{{ $o->end_date ?? 'present' }}</td>
                        <td class="p-3">
                            <a href="{{ route('owner.buildings.tenants.occupancies.edit', [$building->id, $tenant->id, $o->id]) }}"
                                class="text-blue-700 mr-3">Edit</a>

                            <form method="POST"
                                action="{{ route('owner.buildings.tenants.occupancies.destroy', [$building->id, $tenant->id, $o->id]) }}"
                                class="inline" onsubmit="return confirm('Delete this record?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="4">No assignments yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
