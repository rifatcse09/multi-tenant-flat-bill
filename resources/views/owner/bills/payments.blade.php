@extends('layouts.app')
@section('title', 'Bill Payments')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Bill Payments</h1>
                <p class="text-gray-600 mt-1">{{ $bill->flat->building->name }} - Flat {{ $bill->flat->flat_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('owner.payments.create', ['bill_id' => $bill->id]) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Record Payment
                </a>
                <a href="{{ route('owner.bills.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Back to Bills
                </a>
            </div>
        </div>

        @if (session('ok'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('ok') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Bill Summary -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Bill Summary</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Month</label>
                        <div class="text-lg font-semibold">{{ $bill->month->format('F Y') }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                        <div class="text-lg font-semibold">{{ $bill->category->name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tenant</label>
                        <div class="text-lg font-semibold">{{ $bill->tenant->name ?? 'Not assigned' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <div>
                            @if ($bill->status === 'paid')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Paid
                                </span>
                            @elseif($bill->status === 'partial')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Partial
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Unpaid
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-50 p-6 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">${{ number_format($totalDue, 2) }}</div>
                <div class="text-sm text-gray-600">Total Due</div>
                <div class="text-xs text-gray-500 mt-1">
                    Bill: ${{ number_format($bill->amount, 2) }}
                    @if ($bill->due_carry_forward > 0)
                        + Carry Forward: ${{ number_format($bill->due_carry_forward, 2) }}
                    @endif
                </div>
            </div>
            <div class="bg-green-50 p-6 rounded-lg">
                <div class="text-2xl font-bold text-green-600">${{ number_format($totalPaid, 2) }}</div>
                <div class="text-sm text-gray-600">Total Paid</div>
                <div class="text-xs text-gray-500 mt-1">{{ $payments->count() }} payment(s)</div>
            </div>
            <div class="bg-orange-50 p-6 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">${{ number_format($remaining, 2) }}</div>
                <div class="text-sm text-gray-600">Remaining</div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $remaining > 0 ? 'Outstanding balance' : 'Fully paid' }}
                </div>
            </div>
        </div>

        <!-- Payments List -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
            </div>

            @if ($payments->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-gray-500">
                        <p class="text-lg font-medium">No payments recorded</p>
                        <p class="mt-1">Get started by recording the first payment</p>
                        <a href="{{ route('owner.payments.create', ['bill_id' => $bill->id]) }}"
                            class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-600 bg-green-100 hover:bg-green-200">
                            Record first payment
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payment->paid_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $payment->paid_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            ${{ number_format($payment->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ ucfirst($payment->payment_method ?? 'Cash') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('owner.payments.destroy', $payment) }}"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 text-sm font-medium"
                                                onclick="return confirm('Delete this payment?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
