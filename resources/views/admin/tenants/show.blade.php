@extends('layouts.app')
@section('title', 'View Tenant - ' . $tenant->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $tenant->name }}</h1>
                <p class="text-gray-600 mt-1">Tenant Details</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tenants.edit', $tenant) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Edit Tenant
                </a>
                <a href="{{ route('admin.tenants.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Tenant Information -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                        <div class="text-sm text-gray-900">{{ $tenant->name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <div class="text-sm text-gray-900">
                            {{ $tenant->email ?: 'Not provided' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <div class="text-sm text-gray-900">
                            {{ $tenant->phone ?: 'Not provided' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                        <div class="text-sm text-gray-900">
                            {{ $tenant->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $assignmentHistory->count() ?? 0 }}</div>
                <div class="text-sm text-gray-600">Flat Assignments</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $tenant->flats->count() ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Flats</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.tenants.edit', $tenant) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Edit Tenant
                </a>
                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition"
                        onclick="return confirm('Delete {{ $tenant->name }}? This will remove all tenant assignments and related records.')">
                        Delete Tenant
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
