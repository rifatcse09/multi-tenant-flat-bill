@extends('layouts.app')
@section('title', 'Owner Details')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">{{ $owner->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.owners.edit', $owner) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit Owner</a>
            <a href="{{ route('admin.owners.index') }}" class="px-4 py-2 border rounded">Back to List</a>
        </div>
    </div>

    @if (isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Owner Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Owner Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Name:</span>
                    <div class="font-medium">{{ $owner->name }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Email:</span>
                    <div class="font-medium">{{ $owner->email }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Slug:</span>
                    <div class="font-medium">{{ $owner->slug ?? 'Not set' }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Role:</span>
                    <div class="font-medium">
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($owner->role) }}
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Joined:</span>
                    <div class="font-medium">{{ $owner->created_at->format('M d, Y') }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Last Updated:</span>
                    <div class="font-medium">{{ $owner->updated_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Buildings -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Buildings ({{ $buildings->count() }})</h2>
                <a href="{{ route('admin.buildings.create') }}?owner_id={{ $owner->id }}"
                    class="text-blue-600 hover:underline text-sm">+ Add Building</a>
            </div>
            @if ($buildings->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach ($buildings as $building)
                        <div
                            class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">{{ $building->name }}</span>
                                    <a href="{{ route('admin.buildings.show', $building) }}"
                                        class="text-blue-600 hover:underline text-sm">View</a>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">{{ $building->address }}</div>
                                <div class="flex items-center mt-2 text-xs text-gray-500 space-x-4">
                                    <span>{{ $building->flats_count ?? 0 }} flats</span>
                                    <span>{{ $building->tenants_count ?? 0 }} tenants</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="text-gray-500 mb-4">No buildings assigned yet.</p>
                    <a href="{{ route('admin.buildings.create') }}?owner_id={{ $owner->id }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create First Building
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <div class="text-sm text-gray-600">
            <div class="flex items-center justify-between py-2 border-b">
                <span>Account created</span>
                <span>{{ $owner->created_at->format('M d, Y g:i A') }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b">
                <span>Last profile update</span>
                <span>{{ $owner->updated_at->format('M d, Y g:i A') }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <span>Total buildings owned</span>
                <span class="font-medium">{{ $buildings->count() }}</span>
            </div>
        </div>
    </div>
@endsection
