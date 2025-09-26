@extends('layouts.app')
@section('title', 'Edit Assignment')

@section('content')
    <h1 class="text-2xl font-semibold mb-2">
        Edit Assignment: {{ $tenant->name }} — {{ $building->name }}
    </h1>
    <p class="text-gray-600 mb-4">{{ $building->address }}</p>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id]) }}"
            class="px-3 py-2 border rounded hover:bg-gray-50">Back to Assignments</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="POST"
            action="{{ route('owner.buildings.tenants.occupancies.update', [$building->id, $tenant->id, $row->id]) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="flat_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Flat <span class="text-red-500">*</span>
                    </label>
                    <select name="flat_id" id="flat_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('flat_id') border-red-500 @enderror">
                        <option value="">Select Flat</option>
                        @foreach ($flats as $flat)
                            <option value="{{ $flat->id }}"
                                {{ old('flat_id', $row->flat_id) == $flat->id ? 'selected' : '' }}>
                                Flat {{ $flat->flat_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('flat_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ old('start_date', $row->start_date) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date <span class="text-gray-500">(Optional)</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $row->end_date) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave empty for ongoing assignment</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Assignment
                </button>
                <a href="{{ route('owner.buildings.tenants.occupancies.index', [$building->id, $tenant->id]) }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-gray-50 rounded-lg p-4">
        <h3 class="text-sm font-medium text-gray-900 mb-2">Current Assignment Details</h3>
        <div class="text-sm text-gray-600">
            <p><strong>Flat:</strong> {{ $row->flat_number }}</p>
            <p><strong>Start Date:</strong> {{ $row->start_date }}</p>
            <p><strong>End Date:</strong> {{ $row->end_date ?? 'Ongoing' }}</p>
        </div>
    </div>
@endsection
