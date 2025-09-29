@extends('layouts.app')
@section('title', 'Bill Payments')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold mb-2">Bill Payments</h1>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($bill->month)->format('F Y') }} -
                        {{ $bill->category->name }}</p>
                    <p class="text-sm text-gray-500">{{ $bill->flat->building->name }} - Flat {{ $bill->flat->flat_number }}
                    </p>
                </div>
                <a href="{{ route('owner.bills.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    ← Back to Bills
                </a>
            </div>
        </div>

        <!-- Bill Summary -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">${{ number_format($bill->amount, 2) }}</div>
                    <div class="text-sm text-gray-600">Bill Amount</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">${{ number_format($bill->due_carry_forward, 2) }}</div>
                    <div class="text-sm text-gray-600">Carry Forward</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">${{ number_format($totalPaid, 2) }}</div>
                    <div class="text-sm text-gray-600">Total Paid</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">${{ number_format($remaining, 2) }}</div>
                    <div class="text-sm text-gray-600">Remaining</div>
                </div>
            </div>
        </div>

        <!-- Bill Details -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bill Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Property Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><strong>Building:</strong> {{ $bill->flat->building->name }}</div>
                        <div><strong>Flat:</strong> {{ $bill->flat->flat_number }}</div>
                        <div><strong>Category:</strong> {{ $bill->category->name }}</div>
                        <div><strong>Month:</strong> {{ \Carbon\Carbon::parse($bill->month)->format('F Y') }}</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Billing Information</h4>
                    <div class="space-y-2 text-sm">
                        <div><strong>Tenant:</strong> {{ $bill->tenant->name ?? 'No tenant assigned' }}</div>
                        <div><strong>Bill To:</strong> {{ ucfirst($bill->bill_to) }}</div>
                        <div><strong>Status:</strong>
                            <span
                                class="px-2 py-1 text-xs rounded-full
                            @if ($bill->status == 'paid') bg-green-100 text-green-800
                            @elseif($bill->status == 'partial') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </div>
                        @if ($bill->notes)
                            <div><strong>Notes:</strong> {{ $bill->notes }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments List -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                <p class="text-sm text-gray-600 mt-1">All payments received for this bill</p>
            </div>

            @if ($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ ucfirst($payment->payment_method ?? 'Not specified') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $payment->notes ?? 'No notes' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Payment Summary Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">{{ $payments->count() }} payment(s) total</span>
                        <span class="font-semibold">Total Received: ${{ number_format($totalPaid, 2) }}</span>
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Payments Recorded</h3>
                    <p class="text-gray-600 mb-4">This bill hasn't received any payments yet.</p>
                    <a href="{{ route('owner.payments.create', ['bill_id' => $bill->id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Record Payment
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
