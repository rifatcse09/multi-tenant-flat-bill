@extends('layouts.app')
@section('title', 'Add Adjustment')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Add Bill Adjustment</h1>
                <p class="text-gray-600 mt-1">Add an adjustment to increase or decrease a bill amount</p>
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

        <!-- Adjustment Form -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Adjustment Details</h2>
            </div>

            <form method="POST" action="{{ route('owner.adjustments.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Bill Selection -->
                <div>
                    <label for="bill_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Bill <span class="text-red-500">*</span>
                    </label>
                    <select name="bill_id" id="bill_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @if ($bill)
                            <option value="{{ $bill->id }}" selected>
                                {{ $bill->month->format('M Y') }} —
                                {{ $bill->flat->building->name }} - Flat {{ $bill->flat->flat_number }} —
                                {{ $bill->category->name }}
                            </option>
                        @else
                            <option value="">-- Select Bill --</option>
                            @foreach ($bills as $b)
                                <option value="{{ $b->id }}" {{ old('bill_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->month->format('M Y') }} —
                                    {{ $b->flat->building->name }} - Flat {{ $b->flat->flat_number }} —
                                    {{ $b->category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('bill_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adjustment Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Adjustment Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="type" value="increase"
                                {{ old('type') == 'increase' ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Increase</div>
                                <div class="text-sm text-gray-500">Add amount to bill</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="type" value="decrease"
                                {{ old('type') == 'decrease' ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">Decrease</div>
                                <div class="text-sm text-gray-500">Subtract amount from bill</div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adjustment Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Adjustment Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                            value="{{ old('amount') }}"
                            class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="reason" id="reason" required value="{{ old('reason') }}"
                        placeholder="e.g., Late payment fee, Discount for early payment"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('reason')
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
                        Add Adjustment
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Adjustments -->
        @if ($bill && $bill->adjustments->isNotEmpty())
            <div class="mt-6 bg-white rounded-lg shadow-sm border">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Existing Adjustments</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach ($bill->adjustments as $adjustment)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <div
                                        class="font-medium {{ $adjustment->type === 'increase' ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $adjustment->type === 'increase' ? '+' : '-' }}${{ number_format($adjustment->amount, 2) }}
                                    </div>
                                    <div class="text-sm text-gray-600">{{ $adjustment->reason }}</div>
                                    @if ($adjustment->notes)
                                        <div class="text-xs text-gray-500">{{ $adjustment->notes }}</div>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $adjustment->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
