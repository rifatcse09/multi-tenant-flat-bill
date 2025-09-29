@extends('layouts.app')
@section('title', 'Create Bill')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold mb-2">Create New Bill</h1>
            <p class="text-gray-600">Generate a bill for an assigned flat</p>
        </div>

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($flats->count() === 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">No Flats Available for Billing</h3>
                        <div class="text-sm text-yellow-700 mt-2">
                            <p>You cannot create bills because:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>No flats have assigned tenants</li>
                                <li>Bills can only be created for flats with active tenant assignments</li>
                            </ul>
                            <div class="mt-4">
                                <a href="{{ route('owner.buildings.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Assign Tenants to Flats
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <form method="POST" action="{{ route('owner.bills.store') }}">
                    @csrf

                    <!-- Flat Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Flat with Assigned Tenant <span class="text-red-500">*</span>
                        </label>

                        @if ($flats->count() > 0)
                            <select name="flat_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('flat_id') border-red-500 @enderror"
                                required>
                                <option value="">Select a flat...</option>
                                @foreach ($flats as $flat)
                                    <option value="{{ $flat->id }}" {{ old('flat_id') == $flat->id ? 'selected' : '' }}>
                                        {{ $flat->building->name }} - Flat {{ $flat->flat_number }}
                                        @if ($flat->tenants->count() > 0)
                                            ({{ $flat->tenants->first()->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Only flats with assigned tenants are shown</p>
                        @else
                            <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-500">
                                No flats with assigned tenants available
                            </div>
                        @endif

                        @error('flat_id')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bill Category <span class="text-red-500">*</span>
                        </label>
                        <select name="bill_category_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bill_category_id') border-red-500 @enderror"
                            required>
                            <option value="">Select a category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('bill_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bill_category_id')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Month -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bill Month <span class="text-red-500">*</span>
                        </label>
                        <input type="month" name="month" value="{{ old('month', date('Y-m')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('month') border-red-500 @enderror"
                            required>
                        @error('month')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">The tenant must be assigned to the flat for this month</p>
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0"
                                placeholder="0.00"
                                class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-500 @enderror"
                                required>
                        </div>
                        @error('amount')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Optional notes about this bill..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('owner.bills.index') }}"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                            {{ $flats->count() === 0 ? 'disabled' : '' }}>
                            Create Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Information Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-900">Bill Creation Guidelines</h3>
                    <div class="text-sm text-blue-800 mt-1">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Bills can only be created for flats with assigned tenants</li>
                            <li>The system automatically detects the tenant assigned for the selected month</li>
                            <li>Previous unpaid amounts will be carried forward automatically</li>
                            <li>Each flat can have only one bill per category per month</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
