<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50 text-stone-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - BookPlatform' : 'BookPlatform - Read today, lead tomorrow' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|serif:400,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fbf8f3',
                            100: '#f5eee3',
                            200: '#ebd9c4',
                            300: '#ddbe9c',
                            400: '#cd9f74',
                            500: '#b88152',
                            600: '#9b643f',
                            700: '#7a4b33',
                            800: '#633e2d',
                            900: '#523428',
                        },
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full flex flex-col font-sans selection:bg-brand-200 selection:text-brand-900">
    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Tagline -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-stone-900 text-white flex items-center justify-center shadow-sm group-hover:bg-brand-700 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-serif font-bold text-xl tracking-tight text-stone-900">BookPlatform</span>
                            <span class="hidden md:inline-block text-xs text-stone-500 ml-2 font-normal italic">Read today, lead tomorrow.</span>
                        </div>
                    </a>

                    <!-- Main Navigation Links -->
                    <nav class="hidden md:flex items-center gap-1 text-sm font-medium text-stone-600">
                        <a href="{{ route('books.index') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('books.*') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                            Discover
                        </a>
                        <a href="{{ route('pricing') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('pricing') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                            Pricing
                        </a>

                        @auth
                            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('dashboard') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('library.index') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('library.*') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                                My Books
                            </a>
                            <a href="{{ route('progress.index') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('progress.*') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                                Progress
                            </a>
                            <a href="{{ route('streak.index') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('streak.*') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                                Streak
                            </a>
                            <a href="{{ route('quotes.index') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('quotes.*') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                                Quotes
                            </a>
                            <a href="{{ route('recommendations.index') }}" class="px-3 py-1.5 rounded-lg hover:text-stone-900 hover:bg-stone-100 transition {{ request()->routeIs('recommendations.*') ? 'text-stone-900 bg-stone-100 font-semibold' : '' }}">
                                For You
                            </a>
                        @endauth
                    </nav>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Premium Badge / Action -->
                        @if(auth()->user()->isPremium())
                            <div class="relative group">
                                <a href="{{ route('premium.analytics') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span>Premium Active</span>
                                </a>
                            </div>
                        @else
                            <a href="{{ route('pricing') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 text-stone-700 hover:bg-brand-50 hover:text-brand-800 border border-stone-200 transition">
                                <span>Upgrade to Premium</span>
                            </a>
                        @endif

                        <!-- Admin Panel Link -->
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-stone-900 text-white hover:bg-stone-800 transition">
                                <span>Admin Panel</span>
                            </a>
                        @endif

                        <!-- User Profile Menu -->
                        <div class="flex items-center gap-2 pl-2 border-l border-stone-200">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-1 rounded-lg hover:bg-stone-100 transition text-sm font-medium text-stone-700" title="Account Settings">
                                <div class="w-8 h-8 rounded-full bg-brand-200 text-brand-900 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </div>
                                <span class="hidden lg:inline text-xs">{{ auth()->user()->name }}</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-lg hover:bg-stone-100 transition" title="Log out">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Guest Auth Buttons -->
                        <a href="{{ route('login') }}" class="text-sm font-medium text-stone-700 hover:text-stone-900 px-3 py-1.5 rounded-lg hover:bg-stone-100 transition">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-stone-900 hover:bg-brand-700 px-4 py-2 rounded-xl shadow-sm transition">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile sub-nav for authenticated users -->
            @auth
                <div class="md:hidden flex items-center gap-2 py-2 border-t border-stone-100 overflow-x-auto text-xs font-medium text-stone-600">
                    <a href="{{ route('dashboard') }}" class="px-2.5 py-1 rounded-md {{ request()->routeIs('dashboard') ? 'bg-stone-900 text-white' : 'hover:bg-stone-100' }}">Dashboard</a>
                    <a href="{{ route('books.index') }}" class="px-2.5 py-1 rounded-md {{ request()->routeIs('books.*') ? 'bg-stone-900 text-white' : 'hover:bg-stone-100' }}">Discover</a>
                    <a href="{{ route('library.index') }}" class="px-2.5 py-1 rounded-md {{ request()->routeIs('library.*') ? 'bg-stone-900 text-white' : 'hover:bg-stone-100' }}">My Books</a>
                    <a href="{{ route('progress.index') }}" class="px-2.5 py-1 rounded-md {{ request()->routeIs('progress.*') ? 'bg-stone-900 text-white' : 'hover:bg-stone-100' }}">Progress</a>
                    <a href="{{ route('streak.index') }}" class="px-2.5 py-1 rounded-md {{ request()->routeIs('streak.*') ? 'bg-stone-900 text-white' : 'hover:bg-stone-100' }}">Streak</a>
                    <a href="{{ route('quotes.index') }}" class="px-2.5 py-1 rounded-md {{ request()->routeIs('quotes.*') ? 'bg-stone-900 text-white' : 'hover:bg-stone-100' }}">Quotes</a>
                    @if(auth()->user()->isPremium())
                        <a href="{{ route('premium.analytics') }}" class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-900 font-semibold">Analytics</a>
                        <a href="{{ route('premium.plans') }}" class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-900 font-semibold">Plans</a>
                    @endif
                </div>
            @endauth
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center justify-between p-4 mb-4 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-xs" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-base font-bold ml-2">×</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="flex items-center justify-between p-4 mb-4 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl shadow-xs" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700 text-base font-bold ml-2">×</button>
            </div>
        @endif

        @if(session('info'))
            <div class="flex items-center justify-between p-4 mb-4 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-xl shadow-xs" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 text-base font-bold ml-2">×</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl shadow-xs" role="alert">
                <div class="font-semibold mb-1">Please correct the following errors:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-stone-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-stone-900 text-white flex items-center justify-center font-bold text-xs">BP</div>
                        <span class="font-serif font-bold text-lg text-stone-900">BookPlatform</span>
                    </div>
                    <p class="mt-2 text-sm text-stone-600 font-serif italic">
                        “Read today, lead tomorrow.”
                    </p>
                    <p class="mt-2 text-xs text-stone-500 max-w-md leading-relaxed">
                        A digital reading and personal book management platform developed for the INSA Graduation Project. Designed to encourage sustained reading habits, track daily page goals, maintain streaks, and demonstrate a subscription model.
                    </p>
                    <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                        <span>Notice: Demonstration checkout is simulated. No real money is transferred.</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-stone-900">Platform</h4>
                    <ul class="mt-3 space-y-2 text-xs text-stone-600">
                        <li><a href="{{ route('books.index') }}" class="hover:text-stone-900">Discover Catalog</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-stone-900">Pricing & Plans</a></li>
                        @auth
                            <li><a href="{{ route('library.index') }}" class="hover:text-stone-900">Personal Library</a></li>
                            <li><a href="{{ route('streak.index') }}" class="hover:text-stone-900">Streak Tracker</a></li>
                            <li><a href="{{ route('quotes.index') }}" class="hover:text-stone-900">Quotes Collection</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-stone-900">Project Info</h4>
                    <p class="mt-3 text-xs text-stone-500 leading-relaxed">
                        INSA Graduation Project<br>
                        Software Requirements Specification v1.0<br>
                        Stack: Laravel 13 + Blade + SQLite<br>
                        Monetization: Freemium (99 ETB/mo)
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-400">
                <p>&copy; {{ date('Y') }} BookPlatform. INSA Graduation Project Demonstration.</p>
                <p class="mt-2 sm:mt-0">Server-rendered with Laravel Blade</p>
            </div>
        </div>
    </footer>
</body>
</html>
