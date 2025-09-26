@extends('layouts.app')
@section('title', 'Tenant Details')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">{{ $tenant->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit
                Tenant</a>
            <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 border rounded">Back to List</a>
        </div>
    </div>

    @if (isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_buildings'] }}</div>
                <div class="text-sm text-gray-600">Assigned Buildings</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active_occupancies'] }}</div>
                <div class="text-sm text-gray-600">Active Occupancies</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['total_occupancies'] }}</div>
                <div class="text-sm text-gray-600">Total Occupancies</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['past_occupancies'] }}</div>
                <div class="text-sm text-gray-600">Past Occupancies</div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tenant Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Tenant Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Name:</span>
                    <div class="font-medium">{{ $tenant->name }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Email:</span>
                    <div class="font-medium">{{ $tenant->email ?? 'Not provided' }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Phone:</span>
                    <div class="font-medium">{{ $tenant->phone ?? 'Not provided' }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Registered:</span>
                    <div class="font-medium">{{ $tenant->created_at->format('M d, Y') }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Last Updated:</span>
                    <div class="font-medium">{{ $tenant->updated_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Assigned Buildings -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Assigned Buildings ({{ $buildings->count() }})</h2>
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
                                    <span>Owner: {{ $building->owner?->name }}</span>
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
                    <p class="text-gray-500 mb-4">Not assigned to any buildings yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Current Occupancies -->
    @php
        $currentOccupancies = $tenant->flats()->wherePivot('end_date', null)->with('building')->get();
    @endphp
    @if ($currentOccupancies->count() > 0)
        <div class="mt-6 bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Current Occupancies ({{ $currentOccupancies->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600 text-sm">
                        <tr>
                            <th class="p-3">Building</th>
                            <th class="p-3">Flat</th>
                            <th class="p-3">Start Date</th>
                            <th class="p-3">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($currentOccupancies as $flat)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-medium">{{ $flat->building->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $flat->building->address }}</div>
                                </td>
                                <td class="p-3">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Flat {{ $flat->flat_number }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    {{ $flat->pivot->start_date ? \Carbon\Carbon::parse($flat->pivot->start_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="p-3">
                                    @if ($flat->pivot->start_date)
                                        {{ \Carbon\Carbon::parse($flat->pivot->start_date)->diffForHumans(null, true) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Recent Activity -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <div class="text-sm text-gray-600">
            <div class="flex items-center justify-between py-2 border-b">
                <span>Tenant registered</span>
                <span>{{ $tenant->created_at->format('M d, Y g:i A') }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b">
                <span>Last profile update</span>
                <span>{{ $tenant->updated_at->format('M d, Y g:i A') }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <span>Buildings assigned</span>
                <span class="font-medium">{{ $buildings->count() }}</span>
            </div>
        </div>
    </div>
@endsection
