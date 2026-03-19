<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Flat & Bill</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex md:flex-col shadow-sm">
            <div class="px-5 py-6 border-b border-slate-100">
                <a href="/dashboard" class="flex items-center gap-2 text-brand-600 hover:text-brand-700 transition-colors">
                    <x-application-logo :inherit-color="true" />
                </a>
                <p class="mt-1 text-xs text-slate-500 font-medium">Property & Bill Management</p>
            </div>
            <nav class="flex-1 p-4 space-y-0.5">
                <a href="/dashboard" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                @can('admin')
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Admin</div>
                        <a href="{{ route('admin.owners.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Owners</a>
                        <a href="{{ route('admin.buildings.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Buildings</a>
                        <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Tenants</a>
                    </div>
                @endcan
                @can('owner')
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Owner</div>
                        <a href="/owner/buildings" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Buildings</a>
                        <a href="/owner/categories" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Bill Categories</a>
                        <a href="/owner/bills" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Bills</a>
                        <a href="{{ route('owner.payments.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Add Payment</a>
                        <a href="{{ route('owner.adjustments.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">Adjustments</a>
                    </div>
                @endcan
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="relative bg-white border-b border-slate-200 sticky top-0 z-10 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 md:px-8 py-4 flex items-center justify-between">
                    <button class="md:hidden px-3 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">Menu</button>
                    <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name ?? '' }}</span>
                        <form method="POST" action="/logout">@csrf
                            <button type="submit" class="text-sm text-slate-500 hover:text-red-600 font-medium transition-colors">Logout</button>
                        </form>
                    </div>
                </div>
                <!-- Mobile Menu (overlay when open so links are clickable) -->
                <div id="mobileMenu" class="md:hidden hidden absolute left-0 right-0 top-full z-20 border-b border-slate-200 bg-white shadow-lg">
                    <nav class="p-4 space-y-1">
                        <a href="/dashboard" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Dashboard</a>
                        @can('admin')
                            <div class="mt-2 text-xs uppercase text-slate-400 px-3">Admin</div>
                            <a href="/admin/owners" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Owners</a>
                            <a href="/admin/buildings" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Buildings</a>
                            <a href="/admin/tenants" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Tenants</a>
                        @endcan
                        @can('owner')
                            <div class="mt-2 text-xs uppercase text-slate-400 px-3">Owner</div>
                            <a href="/owner/buildings" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Buildings</a>
                            <a href="/owner/categories" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Bill Categories</a>
                            <a href="/owner/bills" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Bills</a>
                            <a href="{{ route('owner.payments.create') }}" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Add Payment</a>
                            <a href="{{ route('owner.adjustments.create') }}" class="block px-3 py-2.5 rounded-lg hover:bg-slate-50">Adjustments</a>
                        @endcan
                    </nav>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 max-w-7xl mx-auto w-full px-4 md:px-8 py-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
