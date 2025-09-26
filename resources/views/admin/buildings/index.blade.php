@extends('layouts.app')
@section('title', 'Manage Buildings')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Buildings Management</h1>
            <p class="text-gray-600 mt-1">Manage all buildings in the system</p>
        </div>
        <a href="{{ route('admin.buildings.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Building
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Buildings</label>
                <input name="q" value="{{ $search ?? '' }}" placeholder="Search by name, address, or owner..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
            @if($search ?? '')
                <div>
                    <a href="{{ route('admin.buildings.index') }}"
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

    <!-- Statistics -->
    @if(isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_buildings'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Buildings</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_flats'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Flats</div>










































































































































@endsection    @endif        </div>            </div>                </div>                    <a href="{{ route('admin.tenants.index') }}" class="text-blue-600 hover:underline">Manage Tenants</a>                    <a href="{{ route('admin.owners.index') }}" class="text-blue-600 hover:underline">Manage Owners</a>                <div class="flex items-center space-x-4">                </div>                    @endif                        matching "{{ $search }}"                    @if($search ?? '')                    of {{ $buildings->total() }} buildings                    Showing {{ $buildings->firstItem() }} to {{ $buildings->lastItem() }}                <div>            <div class="flex items-center justify-between text-sm text-gray-600">        <div class="mt-4 bg-gray-50 rounded-lg p-4">    @if($buildings->count() > 0)    <!-- Results Info -->    @endif        <div class="mt-6">{{ $buildings->appends(['q' => $search ?? ''])->links() }}</div>    @if($buildings->hasPages())    <!-- Pagination -->    </div>        </div>            </table>                </tbody>                    @endforelse                        </tr>                            </td>                                </div>                                    @endif                                        </a>                                            Create first building                                           class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">                                        <a href="{{ route('admin.buildings.create') }}"                                        <p class="mt-1">Get started by creating your first building</p>                                        <p class="text-lg font-medium">No buildings created yet</p>                                    @else                                        </a>                                            Show all buildings                                           class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">                                        <a href="{{ route('admin.buildings.index') }}"                                        <p class="mt-1">Try adjusting your search terms</p>                                        <p class="text-lg font-medium">No buildings found matching "{{ $search }}"</p>                                    @if($search ?? '')                                    </svg>                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">                                <div class="text-gray-500">                            <td class="px-6 py-12 text-center" colspan="6">                        <tr>                    @empty                        </tr>                            </td>                                </div>                                    </form>                                        </button>                                            Delete                                                onclick="return confirm('Delete this building? This will also delete all flats and tenant assignments.')">                                                class="text-red-600 hover:text-red-900 text-sm"                                        <button type="submit"                                         @csrf @method('DELETE')                                    <form method="POST" action="{{ route('admin.buildings.destroy', $building) }}" class="inline">                                       class="text-orange-600 hover:text-orange-900 text-sm">Edit</a>                                    <a href="{{ route('admin.buildings.edit', $building) }}"                                       class="text-green-600 hover:text-green-900 text-sm">Tenants</a>                                    <a href="{{ route('admin.buildings.tenants.index', $building) }}"                                       class="text-blue-600 hover:text-blue-900 text-sm">View</a>                                    <a href="{{ route('admin.buildings.show', $building) }}"                                <div class="flex items-center space-x-3">                            <td class="px-6 py-4">                            </td>                                {{ $building->created_at->format('M d, Y') }}                            <td class="px-6 py-4 text-sm text-gray-500">                            </td>                                </span>                                    {{ $building->tenants_count ?? 0 }} tenants                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">                            <td class="px-6 py-4">                            </td>                                </span>                                    {{ $building->flats_count ?? 0 }} flats                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">                            <td class="px-6 py-4">                            </td>                                @endif                                    <span class="text-red-500 text-sm">No Owner</span>                                @else                                    </div>                                        <div class="text-sm text-gray-500">{{ $building->owner->email }}</div>                                        <div class="text-sm font-medium text-gray-900">{{ $building->owner->name }}</div>                                    <div>                                @if($building->owner)                            <td class="px-6 py-4">                            </td>                                </div>                                    <div class="text-sm text-gray-500">{{ $building->address }}</div>                                    <div class="text-sm font-medium text-gray-900">{{ $building->name }}</div>                                <div>                            <td class="px-6 py-4">                        <tr class="hover:bg-gray-50">                    @forelse($buildings as $building)                <tbody class="bg-white divide-y divide-gray-200">                </thead>                    </tr>                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenants</th>                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flats</th>                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Building</th>                    <tr>                <thead class="bg-gray-50 border-b border-gray-200">            <table class="w-full">        <div class="overflow-x-auto">        </div>            <p class="text-sm text-gray-600 mt-1">All buildings in the system</p>            <h2 class="text-lg font-semibold text-gray-900">Buildings</h2>        <div class="bg-gray-50 px-6 py-4 border-b">    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">    <!-- Buildings List -->    @endif        </div>            </div>                <div class="text-sm text-gray-600">Active Owners</div>                <div class="text-2xl font-bold text-purple-600">{{ $stats['active_owners'] ?? 0 }}</div>            <div class="bg-purple-50 p-4 rounded-lg">            </div>                <div class="text-sm text-gray-600">Total Tenants</div>                <div class="text-2xl font-bold text-yellow-600">{{ $stats['total_tenants'] ?? 0 }}</div>            <div class="bg-yellow-50 p-4 rounded-lg">            </div>
