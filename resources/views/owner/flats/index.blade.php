@extends('layouts.app')
@section('title', 'Flats - ' . $building->name)

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Flats in {{ $building->name }}</h1>
            <p class="text-gray-600 text-sm">{{ $building->address }}</p>
        </div>
        <x-link-button href="{{ route('owner.buildings.flats.create', $building) }}" variant="primary">New Flat</x-link-button>
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
        <x-link-button href="{{ route('owner.buildings.index') }}" variant="secondary">← Back to Buildings</x-link-button>
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
                            <div class="flex items-center gap-2">
                                <x-link-button href="{{ route('owner.flats.edit', $flat) }}" variant="secondary" size="sm">Edit</x-link-button>
                                <form method="POST" action="{{ route('owner.flats.destroy', $flat) }}" class="inline"
                                    onsubmit="return confirm('Delete this flat?');">
                                    @csrf @method('DELETE')
                                    <x-danger-button>Delete</x-danger-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="4">
                            No flats found.
                            <a href="{{ route('owner.buildings.flats.create', $building) }}"
                                class="text-brand-600 hover:underline">
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
