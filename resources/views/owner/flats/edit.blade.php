@extends('layouts.app')
@section('title', 'Edit Flat ' . $flat->flat_number)

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Edit Flat to {{ $flat->building->name }}</h1>

    @if (session('ok'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('ok') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('owner.flats.update', $flat) }}"
        class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
        @csrf @method('PUT')

        <label class="block mb-3">
            <span class="text-sm text-gray-700">Flat number <span class="text-red-500">*</span></span>
            <input name="flat_number" value="{{ old('flat_number', $flat->flat_number) }}"
                class="mt-1 w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('flat_number') border-red-500 @enderror"
                required>
            @error('flat_number')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </label>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="block">
                <span class="text-sm text-gray-700">Flat owner</span>
                <input name="flat_owner_name" value="{{ old('flat_owner_name', $flat->flat_owner_name) }}"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('flat_owner_name') border-red-500 @enderror">
                @error('flat_owner_name')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm text-gray-700">Phone</span>
                <input name="flat_owner_phone" value="{{ old('flat_owner_phone', $flat->flat_owner_phone) }}"
                    class="mt-1 w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('flat_owner_phone') border-red-500 @enderror">
                @error('flat_owner_phone')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </label>
        </div>

        <div class="mt-5 flex gap-2">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Save Changes
            </button>
            <a href="{{ route('owner.buildings.flats.index', $flat->building) }}"
                class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Back
            </a>
        </div>
    </form>

    <div class="mt-6 bg-gray-50 rounded-lg p-4 max-w-xl">
        <h3 class="text-sm font-medium text-gray-900 mb-2">Current Flat Details</h3>
        <div class="text-sm text-gray-600">
            <p><strong>Building:</strong> {{ $flat->building->name }}</p>
            <p><strong>Flat Number:</strong> {{ $flat->flat_number }}</p>
            <p><strong>Owner:</strong> {{ $flat->flat_owner_name ?? 'Not specified' }}</p>
            <p><strong>Phone:</strong> {{ $flat->flat_owner_phone ?? 'Not specified' }}</p>
        </div>
    </div>
@endsection
