@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Edit Category</h1>

    <form method="POST" action="{{ route('owner.categories.update', $category) }}"
        class="bg-white p-6 rounded-lg shadow-sm border max-w-xl">
        @csrf @method('PUT')

        <label class="block mb-5">
            <span class="text-sm text-gray-700">Name</span>
            <input name="name" value="{{ old('name', $category->name) }}" class="mt-1 w-full border p-2 rounded" required>
            @error('name')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </label>

        <div class="flex gap-2">
            <div class="flex gap-2">
            <x-primary-button>Save</x-primary-button>
            <x-link-button href="{{ route('owner.categories.index') }}" variant="secondary">Cancel</x-link-button>
        </div>
        </div>
    </form>
@endsection
