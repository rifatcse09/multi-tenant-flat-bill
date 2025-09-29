@extends('layouts.app')
@section('title', 'Edit Bill')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold mb-2">Edit Bill</h1>
            <p class="text-gray-600">Modify bill details</p>
        </div>

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <form method="POST" action="{{ route('owner.bills.update', $bill) }}">
                    @csrf @method('PUT')

                    <!-- Flat -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Flat</label>
                        <select name="flat_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('flat_id') border-red-500 @enderror"
                            required>
                            @foreach ($flats as $flat)
                                <option value="{{ $flat->id }}"
                                    {{ old('flat_id', $bill->flat_id) == $flat->id ? 'selected' : '' }}>
                                    {{ $flat->building->name }} - Flat {{ $flat->flat_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('flat_id')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="bill_category_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bill_category_id') border-red-500 @enderror"
                            required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('bill_category_id', $bill->bill_category_id) == $category->id ? 'selected' : '' }}>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                        <input type="month" name="month"
                            value="{{ old('month', \Carbon\Carbon::parse($bill->month)->format('Y-m')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('month') border-red-500 @enderror"
                            required>
                        @error('month')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" name="amount" value="{{ old('amount', $bill->amount) }}" step="0.01"
                            min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-500 @enderror"
                            required>
                        @error('amount')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Optional notes..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $bill->notes) }}</textarea>
                        @error('notes')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Bill Info -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Current Bill Details</h3>
                        <div class="text-sm text-gray-600">
                            <p><strong>Tenant:</strong> {{ $bill->tenant?->name ?? 'Not assigned' }}</p>
                            <p><strong>Bill To:</strong> {{ ucfirst($bill->bill_to) }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($bill->status) }}</p>
                            <p><strong>Carry Forward:</strong> ${{ number_format($bill->due_carry_forward, 2) }}</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('owner.bills.index') }}"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            Update Bill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
