@extends('layouts.app')
@section('title', 'Edit Building')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Edit Building: {{ $building->name }}</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.buildings.update', $building) }}"
        class="bg-white p-6 rounded-lg shadow-sm border max-w-2xl">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm text-gray-700">Building Name <span class="text-red-500">*</span></span>
                <input name="name" value="{{ old('name', $building->name) }}"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    required>
                @error('name')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">Owner <span class="text-red-500">*</span></span>
                <select name="owner_id"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('owner_id') border-red-500 @enderror"
                    required>
                    <option value="">Select Owner</option>
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}"
                            {{ old('owner_id', $building->owner_id) == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }} ({{ $owner->email }})
                        </option>
                    @endforeach
                </select>
                @error('owner_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>
        </div>

        <label class="block mt-4">
            <span class="text-sm text-gray-700">Address <span class="text-red-500">*</span></span>
            <textarea name="address" rows="3"
                class="mt-1 w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                required>{{ old('address', $building->address) }}</textarea>
            @error('address')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </label>

        <div class="mt-6 flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Building
            </button>
            <a href="{{ route('admin.buildings.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
@endsection
