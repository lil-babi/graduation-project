@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">Reading Progress & Daily Goal</h1>
                <p class="text-sm text-stone-600 mt-1">Log pages read, track your daily target, and build an unbroken reading record.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('streak.index') }}" class="px-3.5 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold transition">
                    View Streak Record →
                </a>
            </div>
        </div>

        <!-- Top Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Daily Goal Card (SRS FR-040 & FR-045) -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <div class="flex items-center justify-between text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">
                    <span>Today's Reading Target</span>
                    <a href="{{ route('profile.edit') }}" class="text-brand-700 hover:underline">Edit Goal</a>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-serif text-4xl font-bold text-stone-900">{{ $todayPages }}</span>
                    <span class="text-sm font-semibold text-stone-500">/ {{ $dailyGoal }} pages today</span>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-stone-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $goalPercent }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-stone-500">
                        <span>{{ $goalPercent }}% of daily target reached</span>
                        @if($todayPages >= $dailyGoal)
                            <span class="text-emerald-700 font-bold">Goal Met Today! 🎉</span>
                        @else
                            <span>{{ max(0, $dailyGoal - $todayPages) }} pages to go</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Streak Qualifying Card (SRS FR-050 & FR-051) -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-2">Streak Qualification</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $todayPages > 0 ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-400' }} flex items-center justify-center font-bold">
                        🔥
                    </div>
                    <div>
                        <div class="font-serif text-lg font-bold text-stone-900">
                            {{ $todayPages > 0 ? 'Active Reading Day' : 'No Pages Logged Today' }}
                        </div>
                        <p class="text-xs text-stone-500 mt-0.5">
                            {{ $todayPages > 0 ? 'You have preserved your consecutive streak!' : 'Read at least 1 page before midnight to keep your streak.' }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-stone-100 text-[11px] text-stone-500">
                    Rule: Any day with >0 pages qualifies as a reading day.
                </div>
            </div>

            <!-- Total Reading Days -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block mb-2">Lifetime Activity</span>
                <div class="font-serif text-4xl font-bold text-stone-900">{{ $totalDaysRead }}</div>
                <p class="text-xs text-stone-500 mt-1">Calendar days with recorded reading activity</p>
                <div class="mt-4 pt-3 border-t border-stone-100 text-[11px] text-stone-500">
                    Total Pages Read: <strong class="text-stone-900">{{ auth()->user()->totalPagesRead() }}</strong>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Log Activity Form (SRS FR-041 & FR-042) -->
            <div class="lg:col-span-4">
                <div class="bg-white p-6 rounded-2xl border border-stone-200/80 shadow-xs sticky top-24">
                    <h2 class="font-serif text-lg font-bold text-stone-900 mb-1">Log Reading Activity</h2>
                    <p class="text-xs text-stone-500 mb-5">Record your reading session for accurate progress and streaks.</p>

                    <form method="POST" action="{{ route('progress.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="pages_read" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Pages Read (Non-negative > 0)</label>
                            <input id="pages_read" type="number" name="pages_read" min="1" max="2000" required placeholder="e.g. 25" value="{{ old('pages_read') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <div>
                            <label for="reading_date" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Date</label>
                            <input id="reading_date" type="date" name="reading_date" max="{{ $today }}" required value="{{ old('reading_date', $today) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <div>
                            <label for="book_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Associated Book (Optional)</label>
                            <select id="book_id" name="book_id" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                                <option value="">-- General reading session (no specific book) --</option>
                                @foreach($libraryBooks as $item)
                                    <option value="{{ $item->book->id }}" {{ old('book_id') == $item->book->id ? 'selected' : '' }}>
                                        {{ $item->book->title }} (Page {{ $item->current_page }}/{{ $item->book->page_count }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-stone-400 mt-1 block">Associating updates your current page in your library automatically.</span>
                        </div>

                        <div>
                            <label for="minutes_read" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Duration in Minutes (Optional)</label>
                            <input id="minutes_read" type="number" name="minutes_read" min="1" max="1440" placeholder="e.g. 30" value="{{ old('minutes_read') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-stone-800 transition shadow-xs">
                            Submit Reading Entry
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Reading Log History Table (SRS FR-043 & FR-044) -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                        <div>
                            <h2 class="font-serif text-lg font-bold text-stone-900">Recorded Reading History</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Chronological record of verified reading days and pages</p>
                        </div>
                    </div>

                    @if($readingLogs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider font-semibold border-b border-stone-200/60">
                                    <tr>
                                        <th class="py-3.5 px-6">Date</th>
                                        <th class="py-3.5 px-6">Book / Source</th>
                                        <th class="py-3.5 px-6">Pages Read</th>
                                        <th class="py-3.5 px-6">Minutes</th>
                                        <th class="py-3.5 px-6">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100 text-stone-700">
                                    @foreach($readingLogs as $log)
                                        <tr class="hover:bg-stone-50/60 transition">
                                            <td class="py-4 px-6 font-medium text-stone-900">
                                                {{ $log->reading_date->format('M d, Y') }}
                                            </td>
                                            <td class="py-4 px-6">
                                                @if($log->book)
                                                    <a href="{{ route('books.show', $log->book) }}" class="font-medium text-stone-900 hover:text-brand-700">
                                                        {{ $log->book->title }}
                                                    </a>
                                                @else
                                                    <span class="text-stone-400 italic">Independent Reading</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 font-bold text-stone-900">
                                                +{{ $log->pages_read }} pages
                                            </td>
                                            <td class="py-4 px-6 text-stone-500">
                                                {{ $log->minutes_read ? $log->minutes_read . ' min' : '—' }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Qualified
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 border-t border-stone-100">
                            {{ $readingLogs->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center text-stone-500 text-xs">
                            No reading entries logged yet. Log your first reading session on the left to start your record!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
