@extends('layouts.app')
@section('title', 'Edit Bill')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Bill</h1>
                <p class="text-gray-600 mt-1">Update bill details</p>
            </div>
            <a href="{{ route('owner.bills.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Back to Bills
            </a>
        </div>

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Bill Form -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Bill Details</h2>
            </div>

            <form method="POST" action="{{ route('owner.bills.update', $bill) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Flat Selection -->
                <div>
                    <label for="flat_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Flat <span class="text-red-500">*</span>
                    </label>
                    <select name="flat_id" id="flat_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose a flat...</option>
                        @foreach ($flats as $flat)
                            <option value="{{ $flat->id }}"
                                {{ old('flat_id', $bill->flat_id) == $flat->id ? 'selected' : '' }}>
                                {{ $flat->building->name }} - Flat {{ $flat->flat_number }}
                                @if ($flat->tenants->isNotEmpty())
                                    (Tenant: {{ $flat->tenants->first()->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('flat_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Selection -->
                <div>
                    <label for="bill_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Bill Category <span class="text-red-500">*</span>
                    </label>
                    <select name="bill_category_id" id="bill_category_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose a category...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('bill_category_id', $bill->bill_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('bill_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bill Month -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                        Bill Month <span class="text-red-500">*</span>
                    </label>
                    <input type="month" name="month" id="month" required
                        value="{{ old('month', $bill->month->format('Y-m')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Select the month for which this bill is being created</p>
                    @error('month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bill Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Bill Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                            value="{{ old('amount', $bill->amount) }}"
                            class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Any additional notes about this bill...">{{ old('notes', $bill->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('owner.bills.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
