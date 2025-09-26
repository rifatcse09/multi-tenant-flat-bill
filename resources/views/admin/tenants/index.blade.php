@extends('layouts.app')
@section('title','Tenants')

@section('content')
<div class="flex items-center justify-between">
  <h1 class="text-2xl font-semibold">Tenants</h1>
  <a href="{{ route('admin.tenants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Tenant</a>
</div>

<form method="GET" class="mt-4">
  <div class="flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Search name/email/phone" class="border p-2 rounded w-full">
    <button class="px-4 py-2 border rounded">Search</button>
  </div>
</form>

<div class="mt-6 bg-white rounded-lg shadow-sm border overflow-hidden">
  <table class="w-full text-left">
    <thead class="bg-gray-50 text-gray-600 text-sm">
      <tr>
        <th class="p-3">Name</th>
        <th class="p-3">Email</th>
        <th class="p-3">Phone</th>
        <th class="p-3 w-40">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @forelse($tenants as $t)
      <tr>
        <td class="p-3">{{ $t->name }}</td>
        <td class="p-3">{{ $t->email }}</td>
        <td class="p-3">{{ $t->phone }}</td>
        <td class="p-3">
          <a href="{{ route('admin.tenants.edit', $t) }}" class="text-blue-700 mr-3">Edit</a>
          <form method="POST" action="{{ route('admin.tenants.destroy', $t) }}" class="inline"
                onsubmit="return confirm('Delete this tenant?');">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td class="p-3 text-gray-500" colspan="4">No tenants found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $tenants->links() }}</div>
@endsection
