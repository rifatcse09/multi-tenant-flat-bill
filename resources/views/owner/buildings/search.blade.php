@extends('layouts.app')
@section('title', 'Search Buildings')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Search Results for "{{ $q }}"</h1>
        <a href="{{ route('owner.buildings.index') }}" class="px-4 py-2 bg-gray-100 rounded">Back to All Buildings</a>
    </div>

    <form method="GET" action="{{ route('owner.buildings.search') }}" class="mt-4">
        <div class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Search name/address"
                class="border p-2 rounded w-full">
            <button type="submit" class="px-4 py-2 border rounded">Search</button>
        </div>
    </form>

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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="5">
                            No buildings found matching "{{ $q }}".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
