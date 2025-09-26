@extends('layouts.app')
@section('title', 'My Buildings')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">My Buildings</h1>
    </div>

    <form method="GET" class="mt-4">
        <div class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Search name/address"
                class="border p-2 rounded w-full">
            <button type="submit" class="px-4 py-2 border rounded">Search</button>
        </div>
    </form>

    @if (session('ok'))
        <div class="mt-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div>
    @endif

    @if (isset($stats))
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_buildings'] }}</div>
                <div class="text-sm text-gray-600">Total Buildings</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_flats'] }}</div>
                <div class="text-sm text-gray-600">Total Flats</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['occupied_flats'] }}</div>
                <div class="text-sm text-gray-600">Occupied Flats</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['occupancy_rate'] }}%</div>
                <div class="text-sm text-gray-600">Occupancy Rate</div>
            </div>
        </div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Address</th>
                    <th class="p-3">Flats</th>
                    <th class="p-3">Tenants</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($buildings as $b)
                    <tr>
                        <td class="p-3">{{ $b->name }}</td>
                        <td class="p-3">{{ $b->address }}</td>
                        <td class="p-3">{{ $b->flats_count ?? 0 }}</td>
                        <td class="p-3">{{ $b->tenants_count ?? 0 }}</td>
                        <td class="p-3 flex gap-2">
                            <a href="{{ route('owner.buildings.tenants.index', $b) }}"
                                class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-200 transition">
                                View Tenants
                            </a>
                            <a href="{{ route('owner.buildings.flats.index', $b) }}"
                                class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-200 transition">
                                View Flats
                            </a>
                            <a href="{{ route('owner.buildings.flats.create', $b) }}"
                                class="bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 transition">
                                + Add Flat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="5">
                            @if ($q)
                                No buildings found matching "{{ $q }}".
                            @else
                                No buildings assigned yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($buildings->hasPages())
        <div class="mt-4">{{ $buildings->appends(['q' => $q])->links() }}</div>
    @endif
@endsection
