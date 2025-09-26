@extends('layouts.app')
@section('title', 'Flats - ' . $building->name)

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Flats in {{ $building->name }}</h1>
            <p class="text-gray-600 text-sm">{{ $building->address }}</p>
        </div>
        <a href="{{ route('owner.buildings.flats.create', $building) }}"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            New Flat
        </a>
    </div>

    @if (session('ok'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('ok') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 flex gap-2">
        <a href="{{ route('owner.buildings.index') }}" class="px-3 py-2 border rounded hover:bg-gray-50">
            ← Back to Buildings
        </a>
    </div>

    <div class="mt-4 bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm">
                <tr>
                    <th class="p-3">Flat Number</th>
                    <th class="p-3">Owner Name</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($flats as $flat)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $flat->flat_number }}</td>
                        <td class="p-3">{{ $flat->flat_owner_name ?? 'Not specified' }}</td>
                        <td class="p-3">{{ $flat->flat_owner_phone ?? 'Not specified' }}</td>
                        <td class="p-3">
                            <a href="{{ route('owner.flats.edit', $flat) }}" class="text-blue-700 mr-3 hover:underline">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('owner.flats.destroy', $flat) }}" class="inline"
                                onsubmit="return confirm('Delete this flat?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="4">
                            No flats found.
                            <a href="{{ route('owner.buildings.flats.create', $building) }}"
                                class="text-blue-600 hover:underline">
                                Create your first flat
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($flats->hasPages())
        <div class="mt-4">{{ $flats->links() }}</div>
    @endif
@endsection
