@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-stone-500 hover:text-stone-900 font-medium">← Back to Admin Console</a>
                <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Subscription & Financial Audit Report</h1>
                <p class="text-sm text-stone-600 mt-1">Official reporting strictly separating simulated payments from confirmed real revenue (SRS FR-124 & AT-013).</p>
            </div>

            <div class="px-3.5 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Audit Status: Graduation Demonstration Mode Active</span>
            </div>
        </div>

        <!-- Strict Separation Grid (SRS FR-112, FR-113, FR-124) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <!-- Simulated Transactions Card -->
            <div class="p-8 rounded-3xl bg-amber-50/60 border-2 border-amber-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-900">1. Simulated Demonstration Total</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">DEMO ONLY</span>
                    </div>

                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="font-serif text-5xl font-black text-amber-950">{{ number_format($simulatedSum, 2) }}</span>
                        <span class="text-sm font-bold text-amber-800">ETB (Simulated)</span>
                    </div>

                    <p class="text-xs text-amber-900 mt-2 leading-relaxed">
                        Sum of all demonstration transactions recorded during live evaluator walkthroughs. Per SRS FR-111, this is never reported as real cash received.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-amber-200/80 text-xs text-amber-900 font-semibold flex items-center justify-between">
                    <span>Total Demo Transactions:</span>
                    <span>{{ $simulatedCount }} recorded</span>
                </div>
            </div>

            <!-- Confirmed Real Revenue Card (SRS FR-113) -->
            <div class="p-8 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">2. Confirmed Real Revenue</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">ACTUAL AUDITED</span>
                    </div>

                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="font-serif text-5xl font-black text-emerald-700">{{ number_format($realRevenueSum, 2) }}</span>
                        <span class="text-sm font-bold text-stone-600">ETB (Actual Received)</span>
                    </div>

                    <p class="text-xs text-stone-500 mt-2 leading-relaxed">
                        Per SRS requirement FR-113: <em>"Actual revenue shall only include confirmed real payments. If no real payments have been made, actual revenue shall be zero."</em>
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-stone-100 text-xs text-stone-500 flex items-center justify-between">
                    <span>Live Payment Gateway:</span>
                    <span class="font-semibold text-stone-700">None Connected (Simulated Stack)</span>
                </div>
            </div>
        </div>

        <!-- Subscriber Breakdown Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-1">Active Premium Subscribers</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ $activePremiumSubscribers }}</div>
                <p class="text-[11px] text-stone-500 mt-1">Currently entitled to velocity analytics & plans</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-1">Free Readers</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ $freeUsers }}</div>
                <p class="text-[11px] text-stone-500 mt-1">Core reader & library access</p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-1">Expired / Canceled Subscriptions</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ $expiredSubscriptionsCount }}</div>
                <p class="text-[11px] text-stone-500 mt-1">Gracefully returned to standard Free access</p>
            </div>
        </div>

        <!-- Audit Log Table of Transactions (SRS AT-013) -->
        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Simulated Transactions Audit Log</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Every demonstration upgrade produces a traceable database record</p>
                </div>
            </div>

            @if($simulatedTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider font-semibold border-b border-stone-200/60">
                            <tr>
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">User / Account</th>
                                <th class="py-3.5 px-6">Amount</th>
                                <th class="py-3.5 px-6">Classification</th>
                                <th class="py-3.5 px-6">Provider Reference</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-700">
                            @foreach($simulatedTransactions as $tx)
                                <tr class="hover:bg-stone-50/60">
                                    <td class="py-4 px-6 font-mono text-[11px] text-stone-400">#{{ $tx->id }}</td>
                                    <td class="py-4 px-6 font-semibold text-stone-900">
                                        {{ $tx->user->name }}
                                        <span class="block text-[10px] text-stone-400 font-normal">{{ $tx->user->email }}</span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-stone-900">
                                        {{ $tx->amount }} {{ $tx->currency }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            SIMULATED DEMO
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-mono text-[11px] text-stone-600">
                                        {{ $tx->provider_reference }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800">
                                            {{ strtoupper($tx->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-stone-500">
                                        {{ $tx->created_at->format('M d, Y H:i:s') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-stone-100">
                    {{ $simulatedTransactions->links() }}
                </div>
            @else
                <p class="p-10 text-center text-xs text-stone-500">No simulated transactions recorded in the system.</p>
            @endif
        </div>
    </div>
</div>
@endsection
