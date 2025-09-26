@extends('layouts.app')
@section('title', 'My Buildings')

@section('content')
    <h1 class="text-2xl font-semibold">My Buildings</h1>
    @if (session('ok'))
        <div class="mt-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div>
    @endif

    <div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-sm">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Address</th>
                    <th class="p-3">Flats</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($buildings as $b)
                    <tr>
                        <td class="p-3">{{ $b->name }}</td>
                        <td class="p-3">{{ $b->address }}</td>
                        <td class="p-3">{{ $b->flats_count }}</td>
                        <td class="p-3">
                            <a href="{{ route('owner.buildings.tenants.index',$b) }}" class="text-indigo-700">
                            View Tenants
                            </a>
                            <a href="{{ route('owner.buildings.flats.index', $b) }}" class="text-indigo-700 mr-3">
                                View Flats
                            </a>
                            <a href="{{ route('owner.buildings.flats.create', $b) }}" class="text-blue-700">
                                + Add Flat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3 text-gray-500" colspan="3">No buildings assigned yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $buildings->links() }}</div>
@endsection
