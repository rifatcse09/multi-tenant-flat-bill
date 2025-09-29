@extends('layouts.app')
@section('title', 'Bill Categories')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Bill Categories</h1>
            <p class="text-gray-600 mt-1">Manage your billing categories</p>
        </div>
        <a href="{{ route('owner.categories.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Category
        </a>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Your Categories</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $category->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('owner.categories.edit', $category) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                                    <form method="POST" action="{{ route('owner.categories.destroy', $category) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 text-sm font-medium"
                                                onclick="return confirm('Delete {{ $category->name }}? This will affect related bills.')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center text-gray-500" colspan="4">
                                No categories found. <a href="{{ route('owner.categories.create') }}" class="text-blue-600 hover:underline">Create first category</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
