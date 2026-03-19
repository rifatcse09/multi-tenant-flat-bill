<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Flat & Bill') }} — Building & Bill Management</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 via-white to-brand-50/30">
            <a href="/" class="flex items-center justify-center mb-8 group">
                <x-application-logo class="text-brand-600 group-hover:text-brand-700 transition-colors scale-125 sm:scale-150" />
            </a>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white/90 backdrop-blur-sm shadow-xl shadow-slate-200/50 rounded-2xl border border-slate-100 overflow-hidden">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-slate-400">Multi-tenant flat & bill management for property owners</p>
        </div>
    </body>
</html>
