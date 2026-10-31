@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-200 mb-2">
                    <span>★ Premium Feature</span>
                </div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">Advanced Reading Analytics & Velocity</h1>
                <p class="text-sm text-stone-600 mt-1">Deep insights into your reading velocity, habits, and estimated book completion dates.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('premium.plans') }}" class="px-4 py-2 rounded-xl bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800 transition">
                    Personalized Reading Plans →
                </a>
            </div>
        </div>

        <!-- Premium Expansion Notification -->
        <div class="mb-8 p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-400 text-stone-950 font-bold flex items-center justify-center text-lg shrink-0 shadow-xs">
                    ★
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-serif font-bold text-sm text-stone-900">Premium Early-Access Pass Active</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 uppercase">Expanding</span>
                    </div>
                    <p class="text-xs text-stone-600 mt-0.5">
                        Your membership includes current Velocity Analytics plus automatic first-day access to incoming AI tools, audiobook narration, and custom e-reader themes.
                    </p>
                </div>
            </div>
            <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-stone-300 text-stone-800 text-xs font-semibold hover:bg-stone-50 transition shrink-0 shadow-xs">
                <span>View Full Roadmap</span>
                <span>→</span>
            </a>
        </div>

        <!-- Top Velocity Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Metric 1: Daily Velocity -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Daily Reading Velocity</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-serif text-4xl font-black text-stone-900">{{ $analytics['velocity_daily_avg'] }}</span>
                    <span class="text-xs text-stone-500 font-medium">pages/day</span>
                </div>
                <p class="text-[11px] text-stone-500 mt-2">Average pace across all 30 days</p>
            </div>

            <!-- Metric 2: Active Day Velocity -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Active-Day Pace</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-serif text-4xl font-black text-stone-900">{{ $analytics['velocity_active_day_avg'] }}</span>
                    <span class="text-xs text-stone-500 font-medium">pages/active day</span>
                </div>
                <p class="text-[11px] text-stone-500 mt-2">When reading, this is your average speed</p>
            </div>

            <!-- Metric 3: Active Days (30d) -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Consistency Ratio</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-serif text-4xl font-black text-stone-900">{{ $analytics['active_days_last_30'] }}</span>
                    <span class="text-xs text-stone-500 font-medium">/ 30 days</span>
                </div>
                <p class="text-[11px] text-stone-500 mt-2">{{ round(($analytics['active_days_last_30']/30)*100) }}% reading day consistency</p>
            </div>

            <!-- Metric 4: Estimated Time Invested -->
            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Estimated Time Invested</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-serif text-4xl font-black text-stone-900">{{ $analytics['total_estimated_hours'] }}</span>
                    <span class="text-xs text-stone-500 font-medium">hours</span>
                </div>
                <p class="text-[11px] text-stone-500 mt-2">Calculated from lifetime page count</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Forecasts for Currently Reading Books -->
            <div class="lg:col-span-7 space-y-8">
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-stone-900 mb-2">Completion Velocity Forecasts</h2>
                    <p class="text-xs text-stone-500 mb-6">Estimated completion dates based on your current reading velocity.</p>

                    @if(count($analytics['currently_reading_forecasts']) > 0)
                        <div class="space-y-6">
                            @foreach($analytics['currently_reading_forecasts'] as $forecast)
                                <div class="p-5 rounded-2xl bg-stone-50 border border-stone-200/70 space-y-3">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-serif font-bold text-sm text-stone-900">{{ $forecast['book']->title }}</h3>
                                            <p class="text-xs text-stone-500">{{ $forecast['book']->author }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-stone-900 text-white">
                                                Est. {{ $forecast['estimated_finish_date'] }}
                                            </span>
                                            <span class="text-[10px] text-stone-500 block mt-0.5">~{{ $forecast['estimated_days_left'] }} days left</span>
                                        </div>
                                    </div>

                                    <!-- Progress bar -->
                                    <div>
                                        <div class="flex justify-between text-xs text-stone-600 mb-1">
                                            <span>{{ $forecast['current_page'] }} of {{ $forecast['total_pages'] }} pages ({{ $forecast['remaining_pages'] }} remaining)</span>
                                            <span class="font-bold">{{ $forecast['progress_percent'] }}%</span>
                                        </div>
                                        <div class="w-full bg-stone-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-amber-500 h-2 rounded-full transition-all" style="width: {{ $forecast['progress_percent'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-stone-500">
                            No books currently marked as "Reading". Start reading a book from your library to generate completion forecasts!
                        </div>
                    @endif
                </div>

                <!-- Monthly Reading Breakdown -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-stone-900 mb-2">Monthly Reading Trend</h2>
                    <p class="text-xs text-stone-500 mb-6">Pages read over the past 6 months.</p>

                    <div class="grid grid-cols-6 gap-3 items-end h-44 pt-6 border-b border-stone-200">
                        @php
                            $maxPages = max(1, collect($analytics['monthly_trends'])->max('pages'));
                        @endphp
                        @foreach($analytics['monthly_trends'] as $mTrend)
                            @php
                                $heightPercent = round(($mTrend['pages'] / $maxPages) * 100);
                            @endphp
                            <div class="flex flex-col items-center h-full justify-end group">
                                <span class="text-[10px] font-bold text-stone-700 mb-1 group-hover:text-stone-900">{{ $mTrend['pages'] }}p</span>
                                <div class="w-full bg-stone-100 rounded-t-lg relative flex items-end h-full">
                                    <div class="w-full bg-stone-900 group-hover:bg-amber-500 rounded-t-lg transition-all" style="height: {{ max(4, $heightPercent) }}%"></div>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-500 mt-2 truncate max-w-full">{{ $mTrend['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Category Affinity -->
            <div class="lg:col-span-5 space-y-8">
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-stone-900 mb-2">Category Distribution</h2>
                    <p class="text-xs text-stone-500 mb-6">Breakdown of books saved in your personal library by genre.</p>

                    @if(!empty($analytics['category_breakdown']))
                        <div class="space-y-4">
                            @php
                                $totalCatBooks = array_sum($analytics['category_breakdown']);
                            @endphp
                            @foreach($analytics['category_breakdown'] as $catName => $count)
                                @php
                                    $catPercent = $totalCatBooks > 0 ? round(($count / $totalCatBooks) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs font-semibold text-stone-700 mb-1">
                                        <span>{{ $catName }}</span>
                                        <span>{{ $count }} {{ Str::plural('book', $count) }} ({{ $catPercent }}%)</span>
                                    </div>
                                    <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-brand-600 h-2 rounded-full" style="width: {{ $catPercent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-6 text-xs text-stone-500">No category data available yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Features Teaser for Premium Members -->
        <div class="mt-12 p-8 rounded-3xl bg-stone-900 text-white shadow-xl relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-stone-800">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-400 text-stone-950 uppercase tracking-wider mb-2">
                        ★ Rolling Out Next
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-white">Upcoming Features Included In Your Plan</h3>
                    <p class="text-xs text-stone-400 mt-1">We are actively developing and rolling out new features for you.</p>
                </div>
                <span class="text-xs font-semibold text-amber-300 bg-stone-800 px-3.5 py-1.5 rounded-xl border border-stone-700">
                    Zero Extra Fees
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-5 rounded-2xl bg-stone-800/80 border border-stone-700/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-lg">✨</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">In Progress</span>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-white">AI Reading Companion</h4>
                        <p class="text-xs text-stone-400 mt-1.5 leading-relaxed">Interactive Q&A on chapters from Meditations, The Republic, and all classical catalog books.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-stone-700 text-[11px] text-stone-500">Auto-enabled on launch</div>
                </div>

                <div class="p-5 rounded-2xl bg-stone-800/80 border border-stone-700/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-lg">🎧</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-400/20 text-sky-300 border border-sky-400/30">Coming Soon</span>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-white">Audiobook Narration</h4>
                        <p class="text-xs text-stone-400 mt-1.5 leading-relaxed">Integrated voice narration with synchronous word and line highlighting in the reader.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-stone-700 text-[11px] text-stone-500">Auto-enabled on launch</div>
                </div>

                <div class="p-5 rounded-2xl bg-stone-800/80 border border-stone-700/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-lg">📝</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-400/20 text-purple-300 border border-purple-400/30">In Design</span>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-white">Notion & Markdown Export</h4>
                        <p class="text-xs text-stone-400 mt-1.5 leading-relaxed">Export your saved quotes, reviews, and reading notes directly to Obsidian, Notion, or Markdown files.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-stone-700 text-[11px] text-stone-500">Auto-enabled on launch</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
