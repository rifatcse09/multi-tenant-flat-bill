@extends('layouts.app')
@section('title', 'Edit Owner')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Edit Owner: {{ $owner->name }}</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.owners.update', $owner) }}"
        class="bg-white p-6 rounded-lg shadow-sm border max-w-2xl">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm text-gray-700">Name <span class="text-red-500">*</span></span>
                <input name="name" value="{{ old('name', $owner->name) }}"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    required>
                @error('name')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">Email <span class="text-red-500">*</span></span>
                <input name="email" type="email" value="{{ old('email', $owner->email) }}"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    required>
                @error('email')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <label class="block">
                <span class="text-sm text-gray-700">Slug</span>
                <input name="slug" value="{{ old('slug', $owner->slug) }}"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('slug') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Optional unique identifier.</p>
                @error('slug')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">New Password</span>
                <input name="password" type="password"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current password.</p>
                @error('password')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>
        </div>

        <div class="mt-6 flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Owner
            </button>
            <a href="{{ route('admin.owners.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
@endsection
