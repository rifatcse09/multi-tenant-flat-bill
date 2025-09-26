@extends('layouts.app')
@section('title', 'Tenants')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Tenants</h1>
        <a href="{{ route('admin.tenants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Tenant</a>
    </div>

    <form method="GET" class="mt-4">
        <div class="flex gap-2">
            <input name="q" value="{{ $search ?? '' }}" placeholder="Search name/email/phone"
                class="border p-2 rounded w-full">
            <button type="submit" class="px-4 py-2 border rounded">Search</button>
            @if ($search ?? '')
                <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 border rounded bg-gray-100">Clear</a>
            @endif
        </div>
    </form>

    @if (session('ok'))
        <div class="mt-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div>
    @endif

    @if (session('error'))
        <div class="mt-4 bg-red-100 p-3 rounded">{{ session('error') }}</div>
    @endif

    @if (isset($stats))
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_tenants'] }}</div>
                <div class="text-sm text-gray-600">Total Tenants</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active_tenants'] }}</div>
                <div class="text-sm text-gray-600">Active Tenants</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['inactive_tenants'] }}</div>
                <div class="text-sm text-gray-600">Inactive Tenants</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['activity_rate'] }}%</div>
                <div class="text-sm text-gray-600">Activity Rate</div>
            </div>
        </div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tenants as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div
                                        class="h-8 w-8 rounded-full bg-gradient-to-r from-green-500 to-blue-600 flex items-center justify-center">
                                        <span
                                            class="text-white font-semibold text-xs">{{ strtoupper(substr($t->name, 0, 2)) }}</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $t->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">{{ $t->email ?? 'N/A' }}</td>
                        <td class="p-3">{{ $t->phone ?? 'N/A' }}</td>
                        <td class="p-3">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.tenants.show', $t) }}"
                                    class="text-green-700 hover:underline text-sm">View</a>
                                <a href="{{ route('admin.tenants.edit', $t) }}"
                                    class="text-blue-700 hover:underline text-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.tenants.destroy', $t) }}" class="inline"
                                    onsubmit="return confirm('Delete this tenant?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500 text-center" colspan="4">
                            @if ($search ?? '')
                                No tenants found matching "{{ $search }}".
                            @else
                                No tenants found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tenants->hasPages())
        <div class="mt-4">{{ $tenants->appends(['q' => $search ?? ''])->links() }}</div>
    @endif

    @if ($tenants->count() > 0)
        <div class="mt-4 bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div>
                    Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }}
                    of {{ $tenants->total() }} tenants
                    @if ($search ?? '')
                        matching "{{ $search }}"
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    <span>Page {{ $tenants->currentPage() }} of {{ $tenants->lastPage() }}</span>
                </div>
            </div>
        </div>
    @endif
@endsection
