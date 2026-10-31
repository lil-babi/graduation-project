@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-serif text-3xl font-bold text-stone-900">Profile & Reading Preferences</h1>
            <p class="text-sm text-stone-600 mt-1">Configure your daily reading target, account security, and subscription status.</p>
        </div>

        <div class="space-y-8">
            <!-- Account & Preferences Form (SRS FR-015 & FR-016) -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                <h2 class="font-serif text-xl font-bold text-stone-900 mb-1">Personal Details & Reading Goal</h2>
                <p class="text-xs text-stone-500 mb-6">Manage how your daily targets and reading habit are calculated.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Full Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>
                    </div>

                    <!-- Daily Page Goal (SRS FR-040: default 20 pages) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-stone-100">
                        <div>
                            <label for="daily_goal_pages" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">
                                Daily Page Target (Default: 20 pages)
                            </label>
                            <input id="daily_goal_pages" type="number" name="daily_goal_pages" min="1" max="1000" value="{{ old('daily_goal_pages', $user->daily_goal_pages ?? 20) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                            <span class="text-[10px] text-stone-400 mt-1 block">Your daily goal progress bar measures against this number.</span>
                        </div>

                        <div>
                            <label for="favorite_category" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">
                                Favorite Genre / Category
                            </label>
                            <select id="favorite_category" name="favorite_category" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                                <option value="">-- No specific preference --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ ($user->preferences['favorite_category'] ?? '') === $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-stone-400 mt-1 block">Helps curate your "For You" recommendations.</span>
                        </div>
                    </div>

                    <!-- Password Update Section -->
                    <div class="pt-4 border-t border-stone-100 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500">Update Password (Leave blank to keep unchanged)</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="current_password" class="block text-xs font-semibold text-stone-700 mb-1">Current Password</label>
                                <input id="current_password" type="password" name="current_password"
                                    class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                            </div>
                            <div>
                                <label for="new_password" class="block text-xs font-semibold text-stone-700 mb-1">New Password (min 8 characters)</label>
                                <input id="new_password" type="password" name="new_password"
                                    class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-stone-100">
                        <div class="text-[11px] text-stone-400">
                            Role: <strong class="text-stone-700 uppercase">{{ $user->role }}</strong> (Server enforced per FR-016)
                        </div>
                        <button type="submit" class="py-2.5 px-6 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Subscription & Demonstration Entitlements Card (SRS FR-103 & AT-014) -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-4">
                    <div>
                        <h2 class="font-serif text-xl font-bold text-stone-900">Subscription & Demonstration Plan</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Your active entitlement tier and simulated billing history</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->isPremium() ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-stone-100 text-stone-700' }}">
                        {{ $user->isPremium() ? 'Premium Plan Active' : 'Free Plan Active' }}
                    </span>
                </div>

                @if($subscription && $subscription->plan_code === 'premium')
                    <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <div class="font-bold text-xs text-amber-950">BookPlatform Premium (Monthly)</div>
                                <div class="text-[11px] text-amber-800">
                                    Started: {{ $subscription->starts_at->format('M d, Y') }} •
                                    Expires: <strong>{{ $subscription->ends_at?->format('M d, Y') ?? 'Never' }}</strong>
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-amber-800 bg-white px-2.5 py-1 rounded-md border border-amber-200">
                                Mode: {{ ucfirst($subscription->payment_mode) }}
                            </span>
                        </div>

                        <!-- Test Helper for Demonstration (Scenario AT-014) -->
                        <div class="pt-3 border-t border-amber-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <span class="text-[11px] text-amber-900">Demonstration Tools:</span>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('subscription.simulate-expire') }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-stone-900 text-white font-medium hover:bg-stone-800 transition text-[11px]" title="Instantly expire subscription to test scenario AT-014">
                                        Simulate Expiration (Test AT-014)
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('subscription.cancel') }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-white border border-stone-300 text-stone-700 font-medium hover:bg-stone-50 transition text-[11px]" onclick="return confirm('Cancel Premium?');">
                                        Cancel Subscription
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-5 rounded-2xl bg-stone-50 border border-stone-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <div class="font-bold text-xs text-stone-900">Standard Free Plan</div>
                            <div class="text-[11px] text-stone-500">Core library, reading goals, streak tracking, and reviews.</div>
                        </div>
                        <a href="{{ route('subscription.checkout') }}" class="px-4 py-2 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shrink-0">
                            Upgrade to Premium (99 ETB/mo)
                        </a>
                    </div>
                @endif

                <!-- Recent Transactions Table -->
                @if($recentTransactions->isNotEmpty())
                    <div class="mt-6 pt-4 border-t border-stone-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-3">Transaction History</h3>
                        <div class="divide-y divide-stone-100 text-xs">
                            @foreach($recentTransactions as $tx)
                                <div class="py-2.5 flex items-center justify-between">
                                    <div>
                                        <span class="font-semibold text-stone-900">{{ $tx->amount }} {{ $tx->currency }}</span>
                                        <span class="text-stone-400 text-[10px] ml-1">({{ $tx->provider_reference }})</span>
                                        <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold {{ $tx->payment_mode === 'simulated' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ strtoupper($tx->payment_mode) }}
                                        </span>
                                    </div>
                                    <div class="text-stone-500 text-[11px]">
                                        {{ $tx->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
