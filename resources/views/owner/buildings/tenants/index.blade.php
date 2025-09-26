@extends('layouts.app')
@section('title', 'Building Tenants')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $building->name }}</h1>
            <p class="text-gray-600 mt-1">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $building->address }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('owner.buildings.flats.create', $building) }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Flat
            </a>
            <a href="{{ route('owner.buildings.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                ← Back to Buildings
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Tenants</label>
                <input name="q" value="{{ request('q', '') }}" placeholder="Search by name or email"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
            </div>
            @if (request('q'))
                <div>
                    <a href="{{ route('owner.buildings.tenants.index', $building) }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Clear</a>
                </div>
            @endif
        </form>
    </div>

    @if (session('ok'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('ok') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics Dashboard -->
    @if (isset($stats) && is_array($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_tenants'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Tenants</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active_tenants'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Active Tenants</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['occupied_flats'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Occupied Flats</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['occupancy_rate'] ?? 0 }}%</div>
                <div class="text-sm text-gray-600">Occupancy Rate</div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Tenant Assignments & Occupancy</h2>
            <p class="text-sm text-gray-600 mt-1">Manage tenant assignments and view occupancy history</p>
        </div>

        <!-- Tenants Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current
                            Assignment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy
                            History</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                        @php
                            $currentFlat = null;
                            if (isset($tenant->flats)) {
                                $currentFlat = $tenant->flats
                                    ->where('building_id', $building->id)
                                    ->where('pivot.end_date', null)
                                    ->first();
                            }
                            // Get all occupancies for this tenant in this building
                            $allOccupancies = isset($tenant->flats)
                                ? $tenant->flats->where('building_id', $building->id)
                                : collect();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- Tenant Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div
                                            class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                            <span
                                                class="text-white font-semibold text-lg">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-sm text-gray-500">ID: #{{ $tenant->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $tenant->email ?? 'No email' }}</div>
                                <div class="text-sm text-gray-500">{{ $tenant->phone ?? 'No phone' }}</div>
                            </td>

                            <!-- Current Assignment -->
                            <td class="px-6 py-4">
                                @if ($currentFlat)
                                    <div class="flex flex-col">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mb-1">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                            </svg>
                                            Flat {{ $currentFlat->flat_number }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            Since:
                                            {{ $currentFlat->pivot->start_date ? \Carbon\Carbon::parse($currentFlat->pivot->start_date)->format('M d, Y') : 'Unknown' }}
                                        </div>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Not Assigned
                                    </span>
                                @endif
                            </td>

                            <!-- Occupancy History -->
                            <td class="px-6 py-4">
                                @if ($allOccupancies->count() > 0)
                                    <div class="flex flex-col space-y-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $allOccupancies->count() }}
                                            Assignment{{ $allOccupancies->count() > 1 ? 's' : '' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Active: {{ $allOccupancies->where('pivot.end_date', null)->count() }} |
                                            Past: {{ $allOccupancies->where('pivot.end_date', '!=', null)->count() }}
                                        </div>
                                        <a href="{{ route('owner.buildings.tenants.occupancies.index', [$building, $tenant]) }}"
                                            class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            View History
                                        </a>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        <div>No history</div>
                                        <a href="{{ route('owner.buildings.tenants.occupancies.create', [$building, $tenant]) }}"
                                            class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Create First
                                        </a>
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    @if (!$currentFlat)
                                        <!-- Assign to Flat -->
                                        <a href="{{ route('owner.buildings.tenants.occupancies.create', [$building, $tenant]) }}"
                                            class="inline-flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Assign to Flat
                                        </a>
                                    @else
                                        <!-- Quick Actions for Assigned Tenant -->
                                        <div class="flex flex-wrap gap-1">
                                            <a href="{{ route('owner.buildings.tenants.occupancies.create', [$building, $tenant]) }}"
                                                class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                New Assignment
                                            </a>
                                            <a href="{{ route('owner.flats.edit', $currentFlat) }}"
                                                class="inline-flex items-center px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Flat
                                            </a>
                                        </div>
                                    @endif

                                    <!-- Always show full history link -->
                                    <a href="{{ route('owner.buildings.tenants.occupancies.index', [$building, $tenant]) }}"
                                        class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Manage Occupancy
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="5">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    @if (request('q'))
                                        <p class="text-lg font-medium">No tenants found matching "{{ request('q') }}"</p>
                                        <p class="mt-1">Try adjusting your search terms</p>
                                        <a href="{{ route('owner.buildings.tenants.index', $building) }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                            Show all tenants
                                        </a>
                                    @else
                                        <p class="text-lg font-medium">No tenants assigned to this building yet</p>
                                        <p class="mt-1">Contact your administrator to assign tenants to this building</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if (isset($tenants) && method_exists($tenants, 'hasPages') && $tenants->hasPages())
        <div class="mt-6">{{ $tenants->appends(['q' => request('q')])->links() }}</div>
    @endif

    <!-- Results Info -->
    @if (isset($tenants) && $tenants->count() > 0)
        <div class="mt-4 bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div>
                    @if (method_exists($tenants, 'total'))
                        Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }}
                        of {{ $tenants->total() }} tenants
                    @else
                        Showing {{ $tenants->count() }} tenants
                    @endif
                    @if (request('q'))
                        matching "{{ request('q') }}"
                    @endif
                </div>
                <div>
                    <a href="{{ route('owner.buildings.flats.index', $building) }}"
                        class="text-blue-600 hover:underline">View All Flats</a>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions Panel -->
    @if (isset($tenants) && $tenants->count() > 0)
        @php
            $unassignedTenants = $tenants->filter(function ($tenant) use ($building) {
                $currentFlat = null;
                if (isset($tenant->flats)) {
                    $currentFlat = $tenant->flats
                        ->where('building_id', $building->id)
                        ->where('pivot.end_date', null)
                        ->first();
                }
                return !$currentFlat;
            });
        @endphp

        @if ($unassignedTenants->count() > 0)
            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-amber-800">
                            {{ $unassignedTenants->count() }} Unassigned
                            Tenant{{ $unassignedTenants->count() > 1 ? 's' : '' }}
                        </h3>
                        <p class="mt-1 text-sm text-amber-700">
                            These tenants need flat assignments. Click "Assign to Flat" or use the quick buttons below.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($unassignedTenants->take(5) as $tenant)
                                <a href="{{ route('owner.buildings.tenants.occupancies.create', [$building, $tenant]) }}"
                                    class="inline-flex items-center px-3 py-1 bg-amber-200 text-amber-800 text-sm rounded-full hover:bg-amber-300 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Assign {{ $tenant->name }}
                                </a>
                            @endforeach
                            @if ($unassignedTenants->count() > 5)
                                <span class="text-sm text-amber-700 py-1">
                                    ... and {{ $unassignedTenants->count() - 5 }} more
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Bottom Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Building Management</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('owner.buildings.flats.index', $building) }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Manage Flats</div>
                    <div class="text-sm text-gray-500">View and edit all flats in this building</div>
                </div>
            </a>

            <a href="{{ route('owner.buildings.flats.create', $building) }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Add New Flat</div>
                    <div class="text-sm text-gray-500">Create additional flats in this building</div>
                </div>
            </a>

            <a href="{{ route('owner.buildings.index') }}"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Back to Buildings</div>
                    <div class="text-sm text-gray-500">Return to buildings overview</div>
                </div>
            </a>
        </div>
    </div>
@endsection
