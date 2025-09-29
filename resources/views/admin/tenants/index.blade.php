@extends('layouts.app')
@section('title', 'Tenants Management')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Tenants Management</h1>
            <p class="text-gray-600 mt-1">Manage all tenants in the system</p>
        </div>
        <a href="{{ route('admin.tenants.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Tenant
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Tenants</label>
                <input name="q" value="{{ $search ?? '' }}" placeholder="Search by name, email, or phone..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
            @if ($search ?? '')
                <div>
                    <a href="{{ route('admin.tenants.index') }}"
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

    <!-- Statistics -->
    @if (isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_tenants'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Tenants</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active_tenants'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Active Tenants</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['inactive_tenants'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Inactive Tenants</div>
            </div>
        </div>
    @endif

    <!-- Tenants Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Tenants List</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if ($search ?? '')
                    Search results for "{{ $search }}" - {{ $tenants->total() }} tenant(s) found
                @else
                    All tenants in the system - {{ $tenants->total() }} total
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant
                            Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if ($tenant->email)
                                        <div>{{ $tenant->email }}</div>
                                    @endif
                                    @if ($tenant->phone)
                                        <div class="text-gray-500">{{ $tenant->phone }}</div>
                                    @endif
                                    @if (!$tenant->email && !$tenant->phone)
                                        <span class="text-gray-400">No contact info</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $tenant->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.tenants.show', $tenant) }}"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
                                    <a href="{{ route('admin.tenants.edit', $tenant) }}"
                                        class="text-orange-600 hover:text-orange-900 text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium"
                                            onclick="return confirm('Delete {{ $tenant->name }}? This will remove all tenant assignments and related records.')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="4">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    @if ($search ?? '')
                                        <p class="text-lg font-medium">No tenants found matching "{{ $search }}"</p>
                                        <p class="mt-1">Try adjusting your search terms</p>
                                        <a href="{{ route('admin.tenants.index') }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                            Show all tenants
                                        </a>
                                    @else
                                        <p class="text-lg font-medium">No tenants found</p>
                                        <p class="mt-1">Get started by creating the first tenant</p>
                                        <a href="{{ route('admin.tenants.create') }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                            Create first tenant
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
    @if ($tenants->hasPages())
        <div class="mt-6">
            {{ $tenants->appends(['q' => $search ?? ''])->links() }}
        </div>
    @endif

    <!-- Summary -->
    @if ($tenants->count() > 0)
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div>
                    Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }}
                    of {{ $tenants->total() }} tenants
                    @if ($search ?? '')
                        matching "{{ $search }}"
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.buildings.index') }}" class="text-blue-600 hover:underline">Manage
                        Buildings</a>
                </div>
            </div>
        </div>
    @endif
@endsection
