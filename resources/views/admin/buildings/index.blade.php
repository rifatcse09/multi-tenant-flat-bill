@extends('layouts.app')
@section('title', 'Buildings Management')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Buildings Management</h1>
            <p class="text-gray-600 mt-1">Manage all buildings in the system</p>
        </div>
        <a href="{{ route('admin.buildings.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Building
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Buildings</label>
                <input name="q" value="{{ $search ?? '' }}" placeholder="Search by name or address..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
            @if ($search ?? '')
                <div>
                    <a href="{{ route('admin.buildings.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
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

    <!-- Buildings Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Buildings List</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if ($search ?? '')
                    Search results for "{{ $search }}" - {{ $buildings->total() }} building(s) found
                @else
                    All buildings in the system - {{ $buildings->total() }} total
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Building
                            Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flats
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenants
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($buildings as $building)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $building->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $building->address ?: 'No address provided' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($building->owner)
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $building->owner->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $building->owner->email }}</div>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        No Owner
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $building->flats_count ?? 0 }} flats
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $building->tenants_count ?? 0 }} tenants
                                    </span>
                                    <a href="{{ route('admin.buildings.tenants.create', $building) }}"
                                        class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition"
                                        title="Add Tenant">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $building->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.buildings.show', $building) }}"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
                                    <a href="{{ route('admin.buildings.tenants.index', $building) }}"
                                        class="text-green-600 hover:text-green-900 text-sm font-medium">Tenants</a>
                                    <a href="{{ route('admin.buildings.edit', $building) }}"
                                        class="text-orange-600 hover:text-orange-900 text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('admin.buildings.destroy', $building) }}"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium"
                                            onclick="return confirm('Delete {{ $building->name }}? This will remove all flats and tenant assignments.')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="6">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    @if ($search ?? '')
                                        <p class="text-lg font-medium">No buildings found matching "{{ $search }}"
                                        </p>
                                        <p class="mt-1">Try adjusting your search terms</p>
                                        <a href="{{ route('admin.buildings.index') }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                            Show all buildings
                                        </a>
                                    @else
                                        <p class="text-lg font-medium">No buildings found</p>
                                        <p class="mt-1">Get started by creating the first building</p>
                                        <a href="{{ route('admin.buildings.create') }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                            Create first building
                                        </a>
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
    @if ($buildings->hasPages())
        <div class="mt-6">
            {{ $buildings->appends(['q' => $search ?? ''])->links() }}
        </div>
    @endif

@endsection
