@extends('layouts.app')

@section('content')
<div class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-100 text-brand-900 border border-brand-200 mb-3">
                <span>Transparent Freemium Model</span>
            </div>
            <h1 class="font-serif text-3xl sm:text-5xl font-bold text-stone-900 tracking-tight">Simple, Honest Pricing</h1>
            <p class="text-base text-stone-600 mt-3 leading-relaxed">
                Core digital reading and streak tracking remain free forever. Upgrade to Premium for advanced reading velocity analytics and personalized reading schedules.
            </p>

            <!-- Demonstration Notice (SRS FR-111 & Section 7.2) -->
            <div class="mt-6 p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-center justify-center gap-2 max-w-lg mx-auto">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                <span><strong>Demonstration Mode:</strong> Checkout process is simulated. No real currency is transferred or charged.</span>
            </div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch mb-16">
            <!-- Free Plan Card -->
            <div class="p-8 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-2xl font-bold text-stone-900">Free Plan</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-stone-100 text-stone-700">Forever Free</span>
                    </div>
                    <p class="text-xs text-stone-500 mt-1">{{ $plans['free']['description'] }}</p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span class="font-serif text-4xl font-black text-stone-900">0</span>
                        <span class="text-sm font-semibold text-stone-500">{{ $currency }} / forever</span>
                    </div>

                    <!-- Features -->
                    <ul class="mt-8 space-y-3 text-xs text-stone-700">
                        @foreach($plans['free']['features'] as $feat)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-stone-100">
                    @auth
                        @if(!$isPremium)
                            <div class="w-full py-3 px-4 rounded-xl bg-stone-100 text-stone-600 font-semibold text-xs text-center">
                                Current Active Plan
                            </div>
                        @else
                            <form method="POST" action="{{ route('subscription.cancel') }}">
                                @csrf
                                <button type="submit" class="w-full py-2.5 px-4 rounded-xl border border-stone-300 text-stone-700 font-semibold text-xs hover:bg-stone-50 transition" onclick="return confirm('Cancel Premium subscription and return to Free?');">
                                    Downgrade to Free
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="w-full block py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-xs text-center hover:bg-stone-800 transition">
                            Create Free Account
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Premium Plan Card (SRS FR-101: 99 ETB/month) -->
            <div class="p-8 rounded-3xl bg-stone-900 text-white shadow-xl flex flex-col justify-between relative overflow-hidden ring-2 ring-brand-500">
                <div class="absolute top-4 right-4 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-400 text-stone-950 uppercase tracking-wider">
                    {{ $plans['premium']['badge'] ?? 'Most Popular' }}
                </div>

                <div>
                    <h2 class="font-serif text-2xl font-bold text-white">Premium Plan</h2>
                    <p class="text-xs text-stone-400 mt-1">{{ $plans['premium']['description'] }}</p>

                    <div class="mt-6 flex items-baseline gap-1">
                        <span class="font-serif text-5xl font-black text-white">{{ (int)$plans['premium']['price'] }}</span>
                        <span class="text-sm font-semibold text-stone-400">{{ $currency }} / month</span>
                    </div>

                    <!-- Features -->
                    <ul class="mt-8 space-y-3 text-xs text-stone-200">
                        @foreach($plans['premium']['features'] as $feat)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <span>{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-stone-800 space-y-3">
                    <div class="p-2.5 rounded-xl bg-amber-400/10 border border-amber-400/30 text-center">
                        <span class="text-[11px] font-bold text-amber-300">⚡ Expanding Feature Suite</span>
                        <span class="text-[10px] text-stone-400 block mt-0.5">Includes automatic early access to all incoming features</span>
                    </div>

                    @auth
                        @if($isPremium)
                            <div class="p-3 rounded-xl bg-amber-950/80 border border-amber-800 text-center">
                                <span class="text-xs font-bold text-amber-300 block">✓ Active Premium Membership</span>
                                <span class="text-[11px] text-stone-400 mt-0.5 block">
                                    Valid until {{ $activeSubscription?->ends_at?->format('M d, Y') ?? 'Active' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('premium.analytics') }}" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-stone-900 font-semibold text-xs text-center hover:bg-stone-100 transition">
                                    Open Analytics
                                </a>

                                <!-- Demo Expire Trigger (Convenient for testing AT-014) -->
                                <form method="POST" action="{{ route('subscription.simulate-expire') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="py-2.5 px-3 rounded-xl bg-stone-800 text-stone-400 hover:text-amber-400 text-xs font-medium transition" title="Instantly simulate subscription expiration for testing">
                                        Test Expire
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('subscription.checkout') }}" class="w-full block py-3 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 text-stone-950 font-bold text-xs text-center transition shadow-xs">
                                Upgrade to Premium (99 {{ $currency }}/mo)
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full block py-3 px-4 rounded-xl bg-amber-400 hover:bg-amber-300 text-stone-950 font-bold text-xs text-center transition shadow-xs">
                            Log in to Upgrade
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Upcoming Premium Roadmap (SRS Feature Expansion) -->
        <div class="mb-16">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-100 text-brand-900 border border-brand-200 mb-2">
                    <span>🚀 Active Development Roadmap</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-4xl font-bold text-stone-900">More Features Coming Soon to Premium</h2>
                <p class="text-xs sm:text-sm text-stone-600 mt-2">
                    We are actively building new capabilities for serious readers. All active Premium members will receive immediate, automatic access at no additional charge.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingFeatures as $feature)
                    @php
                        $badgeBg = match($feature['badge_color'] ?? 'stone') {
                            'amber' => 'bg-amber-100 text-amber-900 border-amber-200',
                            'blue' => 'bg-sky-100 text-sky-900 border-sky-200',
                            'emerald' => 'bg-emerald-100 text-emerald-900 border-emerald-200',
                            'purple' => 'bg-purple-100 text-purple-900 border-purple-200',
                            default => 'bg-stone-100 text-stone-800 border-stone-200',
                        };
                    @endphp
                    <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between hover:border-brand-300 transition group">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeBg }}">
                                    {{ $feature['badge'] }}
                                </span>
                                <span class="text-xs text-stone-400 font-serif">Planned Q1</span>
                            </div>

                            <h3 class="font-serif font-bold text-base text-stone-900 group-hover:text-brand-800 transition">
                                {{ $feature['title'] }}
                            </h3>

                            <p class="text-xs text-stone-600 mt-2 leading-relaxed">
                                {{ $feature['description'] }}
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-400">
                            <span>Included in 99 ETB plan</span>
                            <span class="font-bold text-brand-700 group-hover:translate-x-0.5 transition-transform">Previewing →</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Frequently Asked Questions / Clarifications -->
        <div class="max-w-3xl mx-auto bg-white p-8 rounded-3xl border border-stone-200/80 shadow-xs">
            <h3 class="font-serif text-xl font-bold text-stone-900 mb-6 text-center">Plan & Pricing Transparency</h3>

            <div class="space-y-6 text-xs text-stone-600">
                <div>
                    <h4 class="font-bold text-stone-900 text-sm mb-1">What happens if my subscription expires?</h4>
                    <p class="leading-relaxed">
                        Per SRS requirement FR-110, you retain complete access to all your saved books, quotes, reviews, and reading streaks. Only the advanced velocity analytics and personalized reading schedule planner are reserved for active Premium members.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-stone-900 text-sm mb-1">How does the simulated checkout work?</h4>
                    <p class="leading-relaxed">
                        To fulfill the INSA graduation demonstration requirement without requiring live payment gateway credentials, the checkout flow records a simulated transaction event (`payment_mode = 'simulated'`) and grants 30 days of active Premium status. In the administrator reporting panel, simulated demo figures are strictly separated from actual confirmed revenue.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
