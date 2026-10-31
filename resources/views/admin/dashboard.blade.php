@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-stone-900 text-white mb-2">
                    <span>Platform Administration</span>
                </div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">Administrator Console</h1>
                <p class="text-sm text-stone-600 mt-1">Manage catalog metadata, upload authorized PDFs, and audit subscription metrics.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.books.create') }}" class="px-4 py-2 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                    + Add New Book
                </a>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-xl bg-white border border-stone-300 text-stone-800 font-semibold text-xs hover:bg-stone-50 transition shadow-xs">
                    Financial Audit Report →
                </a>
            </div>
        </div>

        <!-- Strict Separation of Financials Banner (SRS FR-112 & FR-124) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Metric 1: Total Users -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-1">Registered Accounts</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ $totalUsers }}</div>
                <div class="mt-2 text-xs text-stone-500">
                    {{ $freeUsers }} Free • {{ $activePremiumUsers }} Active Premium
                </div>
            </div>

            <!-- Metric 2: Catalog Books -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-1">Catalog Books</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ $totalBooks }}</div>
                <div class="mt-2 text-xs text-stone-500">
                    Across {{ $totalCategories }} categories
                </div>
            </div>

            <!-- Metric 3: Simulated Revenue Total (SRS FR-111 & FR-112) -->
            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-900 block mb-1">Simulated Demo Volume</span>
                <div class="font-serif text-3xl font-bold text-amber-950">{{ number_format($simulatedRevenueTotal, 2) }} ETB</div>
                <div class="mt-2 text-xs text-amber-800 font-medium">
                    {{ $simulatedTransactionsCount }} Demo Transactions (Simulated)
                </div>
            </div>

            <!-- Metric 4: Confirmed Real Revenue (SRS FR-113 & AT-013) -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-1">Confirmed Real Revenue</span>
                <div class="font-serif text-3xl font-bold text-emerald-700">{{ number_format($realRevenueTotal, 2) }} ETB</div>
                <div class="mt-2 text-xs text-stone-500">
                    Strictly 0.00 ETB (No real gateway attached)
                </div>
            </div>
        </div>

        <!-- Navigation Tabs / Shortcuts -->
        <div class="flex items-center gap-2 border-b border-stone-200 pb-3 mb-8 text-xs font-semibold">
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl bg-stone-900 text-white shadow-xs">
                Overview
            </a>
            <a href="{{ route('admin.books.index') }}" class="px-3.5 py-1.5 rounded-xl text-stone-600 hover:bg-stone-100 transition">
                Manage Books ({{ $totalBooks }})
            </a>
            <a href="{{ route('admin.categories.index') }}" class="px-3.5 py-1.5 rounded-xl text-stone-600 hover:bg-stone-100 transition">
                Categories ({{ $totalCategories }})
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-3.5 py-1.5 rounded-xl text-stone-600 hover:bg-stone-100 transition">
                Users & Roles ({{ $totalUsers }})
            </a>
            <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-1.5 rounded-xl text-stone-600 hover:bg-stone-100 transition">
                Revenue & Audit Report
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Recent Simulated Demo Transactions -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                        <div>
                            <h2 class="font-serif text-lg font-bold text-stone-900">Recent Demonstration Transactions</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Recorded checkout events with mode identification</p>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">
                            Full Audit →
                        </a>
                    </div>

                    @if($recentTransactions->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider font-semibold border-b border-stone-200/60">
                                    <tr>
                                        <th class="py-3 px-6">Subscriber</th>
                                        <th class="py-3 px-6">Amount</th>
                                        <th class="py-3 px-6">Payment Mode</th>
                                        <th class="py-3 px-6">Reference</th>
                                        <th class="py-3 px-6">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100 text-stone-700">
                                    @foreach($recentTransactions as $tx)
                                        <tr class="hover:bg-stone-50/60">
                                            <td class="py-3.5 px-6 font-semibold text-stone-900">
                                                {{ $tx->user->name }}
                                                <span class="block text-[10px] text-stone-400 font-normal">{{ $tx->user->email }}</span>
                                            </td>
                                            <td class="py-3.5 px-6 font-bold text-stone-900">
                                                {{ $tx->amount }} {{ $tx->currency }}
                                            </td>
                                            <td class="py-3.5 px-6">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $tx->payment_mode === 'simulated' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-emerald-100 text-emerald-900' }}">
                                                    {{ strtoupper($tx->payment_mode) }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-6 font-mono text-[11px] text-stone-500">
                                                {{ $tx->provider_reference }}
                                            </td>
                                            <td class="py-3.5 px-6 text-stone-500">
                                                {{ $tx->created_at->format('M d, Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="p-8 text-center text-xs text-stone-500">No transactions recorded yet.</p>
                    @endif
                </div>
            </div>

            <!-- Right: Quick Admin Links -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs space-y-3">
                    <h3 class="font-serif font-bold text-base text-stone-900 mb-2">Administrative Actions</h3>

                    <a href="{{ route('admin.books.create') }}" class="block p-3.5 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200/70 text-xs font-semibold text-stone-800 transition">
                        📖 Upload Book & PDF Metadata
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="block p-3.5 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200/70 text-xs font-semibold text-stone-800 transition">
                        🏷️ Add / Manage Book Categories
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="block p-3.5 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200/70 text-xs font-semibold text-stone-800 transition">
                        👥 Inspect Registered Readers
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="block p-3.5 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200/70 text-xs font-semibold text-stone-800 transition">
                        📊 View Full Audit & Revenue Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
