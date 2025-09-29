@extends('layouts.app')
@section('title', 'Record Payment')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Record Payment</h1>
                <p class="text-gray-600 mt-1">Record a new payment for a bill</p>
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

        @if (session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
                {{ session('info') }}
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

        <!-- No bills warning -->
        @if ($bills->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
                <p class="font-medium">No unpaid bills available</p>
                <p class="text-sm mt-1">You need to have unpaid bills to record payments.</p>
                <a href="{{ route('owner.bills.create') }}" class="text-yellow-600 hover:text-yellow-800 underline">
                    Create a new bill
                </a>
            </div>
        @endif

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Payment Details</h2>
            </div>

            <form method="POST" action="{{ route('owner.payments.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Bill Selection -->
                <div>
                    <label for="bill_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Bill <span class="text-red-500">*</span>
                    </label>
                    <select name="bill_id" id="bill_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        {{ $bills->isEmpty() ? 'disabled' : '' }}>
                        @if ($bill)
                            <!-- If a specific bill is selected (from URL parameter) -->
                            <option value="{{ $bill->id }}" selected>
                                {{ $bill->month->format('M Y') }} —
                                {{ $bill->flat->building->name }} - Flat {{ $bill->flat->flat_number }} —
                                {{ $bill->category->name }} —
                                Due: ${{ number_format($bill->due, 2) }}
                            </option>
                        @else
                            <!-- Show dropdown with all bills -->
                            <option value="">-- Select Bill --</option>
                            @foreach ($bills as $b)
                                <option value="{{ $b->id }}" {{ old('bill_id') == $b->id ? 'selected' : '' }}
                                    data-amount="{{ $b->due }}" data-due="{{ $b->due }}">
                                    {{ $b->month->format('M Y') }} —
                                    {{ $b->flat->building->name }} - Flat {{ $b->flat->flat_number }} —
                                    {{ $b->category->name }} —
                                    Due: ${{ number_format($b->due, 2) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('bill_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selected Bill Info -->
                @if ($bill)
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-medium text-blue-900 mb-2">Selected Bill Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Building:</span> {{ $bill->flat->building->name }}
                            </div>
                            <div>
                                <span class="text-blue-700">Flat:</span> {{ $bill->flat->flat_number }}
                            </div>
                            <div>
                                <span class="text-blue-700">Category:</span> {{ $bill->category->name }}
                            </div>
                            <div>
                                <span class="text-blue-700">Month:</span> {{ $bill->month->format('M Y') }}
                            </div>
                            <div>
                                <span class="text-blue-700">Bill Amount:</span> ${{ number_format($bill->amount, 2) }}
                            </div>
                            <div>
                                <span class="text-blue-700">Total Due:</span>
                                ${{ number_format($bill->amount + $bill->due_carry_forward, 2) }}
                            </div>
                            <div>
                                <span class="text-blue-700">Paid:</span>
                                ${{ number_format($bill->payments->sum('amount'), 2) }}
                            </div>
                            <div>
                                <span class="text-blue-700">Remaining:</span> ${{ number_format($bill->due, 2) }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Payment Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                            value="{{ old('amount', $bill ? $bill->due : '') }}"
                            class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="paid_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="paid_at" id="paid_at" required
                        value="{{ old('paid_at', date('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('paid_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('owner.bills.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" {{ $bills->isEmpty() ? 'disabled' : '' }}
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-fill amount when bill is selected (only if no specific bill is pre-selected)
        @if (!$bill)
            document.getElementById('bill_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.dataset.due) {
                    const dueAmount = parseFloat(selectedOption.dataset.due);
                    if (dueAmount > 0) {
                        document.getElementById('amount').value = dueAmount.toFixed(2);
                    }
                } else {
                    document.getElementById('amount').value = '';
                }
            });
        @endif
    </script>
@endsection
