@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
  <div class="p-5 bg-white rounded-xl shadow-sm border">
    <div class="text-sm text-gray-500">Buildings</div>
    <div class="text-2xl font-semibold mt-1">—</div>
  </div>
  <div class="p-5 bg-white rounded-xl shadow-sm border">
    <div class="text-sm text-gray-500">Flats</div>
    <div class="text-2xl font-semibold mt-1">—</div>
  </div>
  <div class="p-5 bg-white rounded-xl shadow-sm border">
    <div class="text-sm text-gray-500">Unpaid Bills</div>
    <div class="text-2xl font-semibold mt-1">—</div>
  </div>
  <div class="p-5 bg-white rounded-xl shadow-sm border">
    <div class="text-sm text-gray-500">Payments (This Month)</div>
    <div class="text-2xl font-semibold mt-1">—</div>
  </div>
</div>

<div class="mt-8 bg-white rounded-xl shadow-sm border">
  <div class="p-4 border-b font-semibold">Quick Actions</div>
  <div class="p-4 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
    @can('admin')
      <a href="/admin/owners" class="px-4 py-3 rounded border hover:bg-gray-50">Manage Owners</a>
      <a href="/admin/tenants" class="px-4 py-3 rounded border hover:bg-gray-50">Manage Tenants</a>
        <a href="/admin/buildings" class="px-4 py-3 rounded border hover:bg-gray-50">Manage Building</a>
    @endcan
    @can('owner')
      <a href="/owner/buildings" class="px-4 py-3 rounded border hover:bg-gray-50">Manage Buildings</a>
      <a href="/owner/categories" class="px-4 py-3 rounded border hover:bg-gray-50">Bill Categories</a>
      <a href="/owner/bills" class="px-4 py-3 rounded border hover:bg-gray-50">Create/View Bills</a>
    @endcan
  </div>
</div>
@endsection
