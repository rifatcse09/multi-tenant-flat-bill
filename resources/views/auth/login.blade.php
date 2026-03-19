<x-guest-layout>
    <h2 class="text-xl font-semibold text-slate-800 mb-2">Sign in to your account</h2>
    <p class="text-sm text-slate-500 mb-6">Use demo accounts below for quick access</p>

    <!-- Demo Login Options -->
    <div class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Demo Login — Click to fill</p>
        <div class="grid gap-2">
            <button type="button" onclick="fillDemo('admin@example.com','password')" class="w-full text-left px-4 py-3 rounded-lg border border-slate-200 bg-white hover:bg-brand-50 hover:border-brand-200 transition-colors group">
                <span class="font-medium text-slate-800 group-hover:text-brand-700">Super Admin</span>
                <span class="block text-xs text-slate-500 mt-0.5">admin@example.com</span>
            </button>
            <button type="button" onclick="fillDemo('owner1@example.com','password')" class="w-full text-left px-4 py-3 rounded-lg border border-slate-200 bg-white hover:bg-brand-50 hover:border-brand-200 transition-colors group">
                <span class="font-medium text-slate-800 group-hover:text-brand-700">House Owner 1</span>
                <span class="block text-xs text-slate-500 mt-0.5">owner1@example.com</span>
            </button>
            <button type="button" onclick="fillDemo('owner2@example.com','password')" class="w-full text-left px-4 py-3 rounded-lg border border-slate-200 bg-white hover:bg-brand-50 hover:border-brand-200 transition-colors group">
                <span class="font-medium text-slate-800 group-hover:text-brand-700">House Owner 2</span>
                <span class="block text-xs text-slate-500 mt-0.5">owner2@example.com</span>
            </button>
        </div>
        <p class="text-xs text-slate-400 mt-2">Password: <code class="bg-slate-200 px-1 rounded">password</code></p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-brand-600 hover:text-brand-700 font-medium" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full py-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function fillDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('loginForm').submit();
        }
    </script>
</x-guest-layout>
