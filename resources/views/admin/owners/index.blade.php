@extends('layouts.app')
@section('title', 'Owners')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">House Owners</h1>
        <a href="{{ route('admin.owners.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Owner</a>
    </div>

    <form method="GET" class="mt-4">
        <div class="flex gap-2">
            <input name="q" value="{{ $q ?? '' }}" placeholder="Search name/email/slug"
                class="border p-2 rounded w-full">
            <button class="px-4 py-2 border rounded">Search</button>
        </div>
    </form>

    @if (session('ok'))
        <div class="mt-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3 w-40">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($owners as $o)
                    <tr>
                        <td class="p-3">{{ $o->name }}</td>
                        <td class="p-3">{{ $o->email }}</td>
                        <td class="p-3">{{ $o->slug }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.owners.edit', $o) }}" class="text-blue-700 mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.owners.destroy', $o) }}" class="inline"
                                onsubmit="return confirm('Delete this owner?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="4">No owners found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $owners->links() }}</div>
@endsection
