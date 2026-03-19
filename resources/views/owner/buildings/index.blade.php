@extends('layouts.app')
@section('title', 'My Buildings')

@section('content')
    @if (session('ok'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">{{ session('ok') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <h2 class="font-semibold text-slate-800">My Buildings</h2>
            <p class="text-sm text-slate-500 mt-0.5">Manage your properties and flats</p>
        </div>
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-slate-600 text-sm font-medium">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Address</th>
                    <th class="px-6 py-3">Flats</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($buildings as $b)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $b->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $b->address }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $b->flats_count }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <x-link-button href="{{ route('owner.buildings.tenants.index', $b) }}" variant="secondary" size="sm">Tenants</x-link-button>
                                <x-link-button href="{{ route('owner.buildings.flats.index', $b) }}" variant="secondary" size="sm">Flats</x-link-button>
                                <x-link-button href="{{ route('owner.buildings.flats.create', $b) }}" variant="primary" size="sm">+ Add Flat</x-link-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-12 text-center text-slate-500" colspan="4">No buildings assigned yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $buildings->links() }}</div>
@endsection
