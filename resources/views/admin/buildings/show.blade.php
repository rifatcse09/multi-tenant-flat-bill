@extends('layouts.app')
@section('title', 'Building Details')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">{{ $building->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.buildings.edit', $building) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit
                Building</a>
            <a href="{{ route('admin.buildings.index') }}" class="px-4 py-2 border rounded">Back to List</a>
        </div>
    </div>

    @if (isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_flats'] }}</div>
                <div class="text-sm text-gray-600">Total Flats</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['occupied_flats'] }}</div>
                <div class="text-sm text-gray-600">Occupied Flats</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['vacant_flats'] }}</div>
                <div class="text-sm text-gray-600">Vacant Flats</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['occupancy_rate'] }}%</div>
                <div class="text-sm text-gray-600">Occupancy Rate</div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Building Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Building Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Name:</span>
                    <div class="font-medium">{{ $building->name }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Address:</span>
                    <div class="font-medium">{{ $building->address }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Owner:</span>
                    <div class="font-medium">{{ $building->owner->name }}</div>
                    <div class="text-sm text-gray-500">{{ $building->owner->email }}</div>
                </div>
            </div>
        </div>

        <!-- Flats -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Flats ({{ $building->flats->count() }})</h2>
            @if ($building->flats->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach ($building->flats as $flat)
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="font-medium">Flat {{ $flat->flat_number }}</span>
                            <span class="text-sm text-gray-600">
                                {{ $flat->flat_owner_name ?? 'No owner info' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No flats added yet.</p>
            @endif
        </div>

        <!-- Tenants -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Approved Tenants ({{ $building->tenants->count() }})</h2>
            @if ($building->tenants->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach ($building->tenants as $tenant)
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <div>
                                <span class="font-medium">{{ $tenant->name }}</span>
                                <div class="text-sm text-gray-600">{{ $tenant->email }}</div>
                            </div>
                            @if ($tenant->phone)
                                <span class="text-sm text-gray-600">{{ $tenant->phone }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No tenants approved yet.</p>
            @endif
        </div>
    </div>
@endsection
