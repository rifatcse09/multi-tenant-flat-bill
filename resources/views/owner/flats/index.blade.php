@extends('layouts.app')
@section('title','Flats of '.$building->name)

@section('content')
<h1 class="text-2xl font-semibold mb-2">Flats of {{ $building->name }}</h1>
<p class="text-gray-600 mb-4">{{ $building->address }}</p>

@if(session('ok')) <div class="mb-4 bg-green-100 p-3 rounded">{{ session('ok') }}</div> @endif

<a href="{{ route('owner.buildings.flats.create',$building) }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Flat</a>

<div class="mt-4 bg-white rounded-lg shadow-sm border overflow-hidden">
  <table class="w-full text-left">
    <thead class="bg-gray-50 text-gray-600 text-sm">
      <tr>
        <th class="p-3">Flat #</th>
        <th class="p-3">Flat Owner</th>
        <th class="p-3">Phone</th>
        <th class="p-3 w-40">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($flats as $f)
      <tr>
        <td class="p-3 font-medium"> {{ $f->flat_number }} </td>
        <td class="p-3"> {{ $f->flat_owner_name ?? '—' }} </td>
        <td class="p-3"> {{ $f->flat_owner_phone ?? '—' }} </td>
        <td class="p-3">
          <a href="{{ route('owner.flats.edit',$f) }}" class="text-blue-700 mr-3">Edit</a>
          <form method="POST" action="{{ route('owner.flats.destroy',$f) }}" class="inline"
                onsubmit="return confirm('Delete this flat?');">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td class="p-3 text-gray-500" colspan="4">No flats yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $flats->links() }}</div>
@endsection
