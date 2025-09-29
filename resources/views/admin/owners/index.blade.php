@extends('layouts.app')
@section('title', 'Owners Management')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Owners Management</h1>
            <p class="text-gray-600 mt-1">Manage property owners in the system</p>
        </div>
        <a href="{{ route('admin.owners.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Owner
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Owners</label>
                <input name="q" value="{{ $search ?? '' }}" placeholder="Search by name or email..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
            @if ($search ?? '')
                <div>
                    <a href="{{ route('admin.owners.index') }}"
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

    <!-- Owners Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Property Owners</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if ($search ?? '')
                    Search results for "{{ $search }}" - {{ $owners->total() }} owner(s) found
                @else
                    All property owners in the system - {{ $owners->total() }} total
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner
                            Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buildings
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($owners as $owner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $owner->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $owner->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $owner->buildings_count ?? 0 }} buildings
                                    </span>
                                    <a href="{{ route('admin.buildings.create', ['owner_id' => $owner->id]) }}"
                                        class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition"
                                        title="Add Building">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $owner->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.owners.edit', $owner) }}"
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('admin.owners.destroy', $owner) }}"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium"
                                            onclick="return confirm('Delete {{ $owner->name }}? This will remove all their buildings.')">
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
                                        <p class="text-lg font-medium">No owners found matching "{{ $search }}"</p>
                                        <p class="mt-1">Try adjusting your search terms</p>
                                        <a href="{{ route('admin.owners.index') }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                            Show all owners
                                        </a>
                                    @else
                                        <p class="text-lg font-medium">No owners found</p>
                                        <p class="mt-1">Get started by creating the first owner</p>
                                        <a href="{{ route('admin.owners.create') }}"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                            Create first owner
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
    @if ($owners->hasPages())
        <div class="mt-6">
            {{ $owners->appends(['q' => $search ?? ''])->links() }}
        </div>
    @endif
@endsection
