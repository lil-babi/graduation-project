@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Greeting Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">Welcome, {{ $user->name }}</h1>
                <p class="text-sm text-stone-600 mt-1">Here is your daily reading pulse and personalized reading progress.</p>
            </div>

            <div class="flex items-center gap-3">
                @if($isPremium)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-200">
                        <span>★ Premium Active</span>
                    </span>
                    <a href="{{ route('premium.analytics') }}" class="px-3.5 py-1.5 rounded-xl bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800 transition">
                        Reading Velocity
                    </a>
                @else
                    <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-stone-100 hover:bg-stone-200 text-stone-800 border border-stone-200 transition">
                        <span>Free Plan • Upgrade for Analytics</span>
                    </a>
                @endif
                <a href="{{ route('progress.index') }}" class="px-3.5 py-1.5 rounded-xl bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800 transition">
                    + Log Pages
                </a>
            </div>
        </div>

        <!-- Premium Feature Status & Roadmap Banner -->
        @if($isPremium)
            <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-stone-900 via-stone-800 to-stone-900 text-white shadow-sm border border-stone-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-400 text-stone-950 flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                        ★
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-serif font-bold text-sm text-white">Premium VIP Membership Active</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30 uppercase">More Features Incoming</span>
                        </div>
                        <p class="text-xs text-stone-400 mt-0.5 leading-relaxed">
                            Your membership includes Velocity Analytics & Custom Plans. New tools—including an AI Chapter Companion, Audiobook Narration, and Markdown Note Export—are actively rolling out.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('pricing') }}" class="px-3.5 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 text-xs font-medium transition border border-stone-700">
                        Explore Roadmap →
                    </a>
                </div>
            </div>
        @else
            <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 via-orange-50 to-stone-50 border border-amber-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center text-xl shrink-0">
                        🚀
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-serif font-bold text-sm text-stone-900">Exciting Premium Features in Active Development</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 uppercase">Coming Soon</span>
                        </div>
                        <p class="text-xs text-stone-600 mt-0.5 leading-relaxed">
                            We are building an AI Reading Companion, Synchronized Audiobook Narration, and Notion/Markdown Exports. Upgrade for 99 ETB/mo to secure early access.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('pricing') }}" class="px-4 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold transition shadow-xs">
                        View Premium Roadmap →
                    </a>
                </div>
            </div>
        @endif

        <!-- Pulse Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Metric 1: Today's Goal -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Today's Progress</span>
                <div class="flex items-baseline gap-2">
                    <span class="font-serif text-3xl font-bold text-stone-900">{{ $todayPages }}</span>
                    <span class="text-xs text-stone-500 font-medium">/ {{ $dailyGoal }} pages</span>
                </div>
                <div class="w-full bg-stone-100 rounded-full h-2 mt-3 overflow-hidden">
                    <div class="bg-emerald-600 h-2 rounded-full transition-all" style="width: {{ $goalPercent }}%"></div>
                </div>
                <div class="mt-2 text-[11px] text-stone-500">
                    {{ $goalPercent }}% of daily target reached
                </div>
            </div>

            <!-- Metric 2: Current Streak -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Reading Streak</span>
                <div class="flex items-center gap-2">
                    <span class="text-3xl">🔥</span>
                    <span class="font-serif text-3xl font-bold text-stone-900">{{ $currentStreak }}</span>
                    <span class="text-xs text-stone-500 font-medium">days</span>
                </div>
                <div class="mt-3 text-[11px] text-stone-500">
                    Best: <strong class="text-stone-800">{{ $longestStreak }} days</strong>
                </div>
            </div>

            <!-- Metric 3: Books Completed -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Books Completed</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ $user->booksReadCount() }}</div>
                <div class="mt-3 text-[11px] text-stone-500">
                    Saved in Library: <strong class="text-stone-800">{{ $user->userBooks()->count() }}</strong>
                </div>
            </div>

            <!-- Metric 4: Total Pages Read -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2">Total Pages Read</span>
                <div class="font-serif text-3xl font-bold text-stone-900">{{ number_format($user->totalPagesRead()) }}</div>
                <div class="mt-3 text-[11px] text-stone-500">
                    Est. Reading Time: <strong class="text-stone-800">{{ round(($user->totalPagesRead() * 1.5)/60, 1) }} hrs</strong>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Main Area: Currently Reading & Activity -->
            <div class="lg:col-span-8 space-y-10">
                <!-- Currently Reading Section -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-6">
                        <div>
                            <h2 class="font-serif text-xl font-bold text-stone-900">Currently Reading</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Pick up where you left off</p>
                        </div>
                        <a href="{{ route('library.index', ['status' => 'reading']) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900">
                            View All ({{ $user->userBooks()->where('status', 'reading')->count() }})
                        </a>
                    </div>

                    @if($currentlyReading->isNotEmpty())
                        <div class="space-y-6">
                            @foreach($currentlyReading as $item)
                                @php $book = $item->book; @endphp
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-stone-50/80 border border-stone-200/70">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-22 shrink-0 bg-stone-200 rounded-xl overflow-hidden">
                                            @if($book->cover_path)
                                                <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full p-2 flex flex-col justify-between bg-stone-800 text-white text-[8px]">
                                                    <span class="font-bold line-clamp-2">{{ $book->title }}</span>
                                                    <span class="text-stone-400 truncate">{{ $book->author }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold text-brand-700 uppercase tracking-wider">{{ $book->category->name }}</span>
                                            <h3 class="font-serif font-bold text-sm text-stone-900 hover:text-brand-700">
                                                <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                            </h3>
                                            <p class="text-xs text-stone-500">{{ $book->author }}</p>

                                            <!-- Progress detail -->
                                            <div class="mt-2 flex items-center gap-3 text-xs text-stone-600">
                                                <span>{{ $item->current_page }} of {{ $book->page_count }} pages</span>
                                                <span class="font-bold text-stone-900">({{ $book->userProgressPercent(auth()->user()) }}%)</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if($book->pdf_path)
                                            <a href="{{ route('books.read', $book) }}" class="px-3.5 py-2 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition">
                                                Read PDF
                                            </a>
                                        @endif
                                        <a href="{{ route('books.show', $book) }}" class="px-3 py-2 rounded-xl bg-white border border-stone-300 text-stone-700 font-semibold text-xs hover:bg-stone-100 transition">
                                            Update
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-xs text-stone-500">
                            You don't have any books marked as "Currently Reading".
                            <a href="{{ route('books.index') }}" class="font-bold text-stone-900 underline ml-1">Browse the catalog</a> to start reading!
                        </div>
                    @endif
                </div>

                <!-- Recent Reading Activity -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-4">
                        <div>
                            <h2 class="font-serif text-xl font-bold text-stone-900">Recent Reading Activity</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Latest verified reading logs</p>
                        </div>
                        <a href="{{ route('progress.index') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900">
                            View All History
                        </a>
                    </div>

                    @if($recentLogs->isNotEmpty())
                        <div class="divide-y divide-stone-100">
                            @foreach($recentLogs as $log)
                                <div class="py-3 flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-medium text-stone-900">
                                            {{ $log->book ? $log->book->title : 'Independent Reading Session' }}
                                        </div>
                                        <div class="text-[11px] text-stone-400 mt-0.5">{{ $log->reading_date->format('l, F j, Y') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-emerald-700">+{{ $log->pages_read }} pages</div>
                                        @if($log->minutes_read)
                                            <div class="text-[11px] text-stone-400">{{ $log->minutes_read }} min</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-6 text-xs text-stone-500">No reading sessions logged yet.</p>
                    @endif
                </div>
            </div>

            <!-- Right Column: Recommendations with Reason Tags & Shortcuts -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Recommendations (SRS FR-080 to FR-083) -->
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-serif text-base font-bold text-stone-900">Recommended For You</h2>
                        <span class="text-[10px] text-stone-400">Rule-based Curated</span>
                    </div>

                    <div class="space-y-4">
                        @foreach($recommendations as $rec)
                            @php $rBook = $rec['book']; @endphp
                            <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200/60">
                                <div class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-brand-100 text-brand-900 mb-1.5">
                                    {{ $rec['badge'] }}
                                </div>
                                <h4 class="font-serif font-bold text-xs text-stone-900 hover:text-brand-700">
                                    <a href="{{ route('books.show', $rBook) }}">{{ $rBook->title }}</a>
                                </h4>
                                <p class="text-[11px] text-stone-500 mt-0.5">{{ $rBook->author }} • {{ $rBook->category->name }}</p>
                                <p class="text-[10px] text-stone-600 mt-2 font-medium italic">
                                    “{{ $rec['reason'] }}”
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shortcuts Card -->
                <div class="bg-stone-50 p-6 rounded-3xl border border-stone-200 text-xs space-y-3">
                    <h3 class="font-bold text-stone-900 uppercase tracking-wider text-[11px]">Quick Navigation</h3>
                    <ul class="space-y-2 text-stone-600 font-medium">
                        <li><a href="{{ route('library.index') }}" class="hover:text-stone-900 flex items-center justify-between"><span>My Books Library</span><span>→</span></a></li>
                        <li><a href="{{ route('streak.index') }}" class="hover:text-stone-900 flex items-center justify-between"><span>Streak Calendar</span><span>→</span></a></li>
                        <li><a href="{{ route('quotes.index') }}" class="hover:text-stone-900 flex items-center justify-between"><span>Saved Quotes</span><span>→</span></a></li>
                        <li><a href="{{ route('reviews.index') }}" class="hover:text-stone-900 flex items-center justify-between"><span>My Book Reviews</span><span>→</span></a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-stone-900 flex items-center justify-between"><span>Account & Goal Preferences</span><span>→</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
