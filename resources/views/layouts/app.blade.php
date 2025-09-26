<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r hidden md:block">
            <div class="px-6 py-5 border-b">
                <div class="text-xl font-bold">Flat&Bill</div>
                <div class="text-xs text-gray-500">Multi-tenant</div>
            </div>
            <nav class="p-4 space-y-1">
                <a href="/dashboard" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                @can('admin')
                    <div class="mt-3 text-xs uppercase text-gray-400 px-3">Admin</div>
                    <a href="{{ route('admin.owners.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Owners</a>
                    <a href="{{ route('admin.buildings.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Buildings</a>
                    <a href="{{ route('admin.tenants.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Tenants</a>
                @endcan
                @can('owner')
                    <div class="mt-3 text-xs uppercase text-gray-400 px-3">Owner</div>
                    <a href="/owner/buildings" class="block px-3 py-2 rounded hover:bg-gray-100">Buildings</a>
                    <a href="/owner/categories" class="block px-3 py-2 rounded hover:bg-gray-100">Bill Categories</a>
                    <a href="/owner/bills" class="block px-3 py-2 rounded hover:bg-gray-100">Bills</a>
                @endcan
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header class="bg-white border-b">
                <div class="max-w-7xl mx-auto px-4 md:px-8 py-4 flex items-center justify-between">
                    <button class="md:hidden px-3 py-2 border rounded"
                        onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">Menu</button>
                    <div class="font-semibold">@yield('title', 'Dashboard')</div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name ?? '' }}</span>
                        <form method="POST" action="/logout">@csrf<button
                                class="text-red-600 hover:underline">Logout</button></form>
                    </div>
                </div>
                <!-- Mobile Menu -->
                <div id="mobileMenu" class="md:hidden hidden border-t bg-white">
                    <nav class="p-3 space-y-1">
                        <a href="/dashboard" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a>
                        @can('admin')
                            <div class="mt-2 text-xs uppercase text-gray-400 px-3">Admin</div>
                            <a href="/admin/owners" class="block px-3 py-2 rounded hover:bg-gray-100">Owners</a>
                            <a href="/admin/tenants" class="block px-3 py-2 rounded hover:bg-gray-100">Tenants</a>
                        @endcan
                        @can('owner')
                            <div class="mt-2 text-xs uppercase text-gray-400 px-3">Owner</div>
                            <a href="/owner/buildings" class="block px-3 py-2 rounded hover:bg-gray-100">Buildings</a>
                            <a href="/owner/categories" class="block px-3 py-2 rounded hover:bg-gray-100">Bill
                                Categories</a>
                            <a href="/owner/bills" class="block px-3 py-2 rounded hover:bg-gray-100">Bills</a>
                        @endcan
                    </nav>
                </div>
            </header>

            <!-- Content -->
            <main class="max-w-7xl mx-auto w-full px-4 md:px-8 py-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
