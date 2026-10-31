@extends('layouts.app')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white p-8 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-stone-900 text-white flex items-center justify-center mx-auto mb-3 shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h2 class="font-serif text-2xl font-bold text-stone-900">Create Your Account</h2>
                <p class="text-xs text-stone-500 mt-1">Start tracking books, reading goals, and streaks today</p>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Password (min 8 characters)</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900">
                </div>

                <div class="text-xs text-stone-500 py-1">
                    By registering, you receive instant access to the Free plan (20 pages/day default goal, personal library, reading streaks).
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-stone-800 transition shadow-xs">
                    Register Account
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-stone-600">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-stone-900 hover:underline">Log in here</a>
            </div>
        </div>
    </div>
</div>
@endsection
