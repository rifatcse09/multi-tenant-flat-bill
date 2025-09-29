@extends('layouts.app')
@section('title', 'Bills')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Bills</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('owner.payments.create') }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Add Payment
            </a>
            <a href="{{ route('owner.bills.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Bill
            </a>
        </div>
    </div>

    @if (session('ok'))
        <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded">
            {{ session('ok') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input name="q" value="{{ $filters['q'] }}" placeholder="Search tenant name/email"
                    class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Flat</label>
                <select name="flat_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Flats</option>
                    @foreach ($flats as $flat)
                        <option value="{{ $flat->id }}" {{ $filters['flat_id'] == $flat->id ? 'selected' : '' }}>
                            {{ $flat->building->name }} - Flat {{ $flat->flat_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $filters['category_id'] == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="unpaid" {{ $filters['status'] == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ $filters['status'] == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ $filters['status'] == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            {{ \Carbon\Carbon::parse($bill->month)->format('M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $bill->flat->building->name ?? 'N/A' }} - Flat {{ $bill->flat->flat_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $bill->category->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $bill->tenant->name ?? 'No tenant' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span>${{ number_format($bill->amount, 2) }}</span>
                                @if ($bill->status !== 'paid')
                                    <a href="{{ route('owner.payments.create', ['bill_id' => $bill->id]) }}"
                                        class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition"
                                        title="Add Payment">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            @if ($bill->due_carry_forward > 0)
                                <div class="text-xs text-gray-500">
                                    + ${{ number_format($bill->due_carry_forward, 2) }} carry
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($bill->status == 'unpaid')
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Unpaid
                                </span>
                            @elseif($bill->status == 'partial')
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Partial
                                </span>
                            @else
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('owner.bills.edit', $bill) }}"
                                    class="text-blue-600 hover:text-blue-900">Edit</a>

                                @if ($bill->payments()->exists())
                                    <a href="{{ route('owner.bills.payments', $bill) }}"
                                        class="text-green-600 hover:text-green-900">Payments</a>
                                @endif

                                @if ($bill->status === 'unpaid')
                                    <form method="POST" action="{{ route('owner.bills.destroy', $bill) }}" class="inline"
                                        onsubmit="return confirm('Delete this unpaid bill? This action cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Only unpaid bills can be deleted">
                                        Delete
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-12 text-center text-gray-500" colspan="7">
                            No bills found. <a href="{{ route('owner.bills.create') }}"
                                class="text-blue-600 hover:underline">Create your first bill</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($bills->hasPages())
        <div class="mt-6">
            {{ $bills->links() }}
        </div>
    @endif
@endsection
