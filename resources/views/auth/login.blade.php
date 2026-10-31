@extends('layouts.app')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white p-8 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-stone-900 text-white flex items-center justify-center mx-auto mb-3 shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h2 class="font-serif text-2xl font-bold text-stone-900">Welcome Back</h2>
                <p class="text-xs text-stone-500 mt-1">Sign in to your personal reading library</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-stone-600">
                        <input type="checkbox" name="remember" class="rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-stone-800 transition shadow-xs">
                    Sign In
                </button>
            </form>

            <!-- Quick Demo Accounts (Handover & Evaluation Convenience) -->
            <div class="mt-8 pt-6 border-t border-stone-200">
                <span class="block text-[11px] font-semibold text-stone-400 uppercase tracking-wider text-center mb-3">
                    Graduation Demo Accounts (Click to Fill)
                </span>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <button type="button" onclick="fillDemo('admin@bookplatform.et', 'password')"
                        class="p-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 font-medium text-center transition">
                        Admin
                    </button>
                    <button type="button" onclick="fillDemo('reader@bookplatform.et', 'password')"
                        class="p-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 font-medium text-center transition">
                        Free Reader
                    </button>
                    <button type="button" onclick="fillDemo('premium@bookplatform.et', 'password')"
                        class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-medium border border-amber-200 text-center transition">
                        Premium
                    </button>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-stone-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-stone-900 hover:underline">Register here</a>
            </div>
        </div>
    </div>
</div>

<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
