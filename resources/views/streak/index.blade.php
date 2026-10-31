@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-200 mb-3">
                <span>Reading Streak Tracker</span>
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">Consecutive Reading Habits</h1>
            <p class="text-sm text-stone-600 mt-2">
                Reading at least 1 page each calendar day sustains your streak. Consistent daily reading compounds over time.
            </p>
        </div>

        <!-- Main Streak Cards -->
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <!-- Current Streak Card -->
            <div class="p-8 rounded-3xl bg-white border border-stone-200/80 shadow-xs text-center flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Current Reading Streak</span>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <span class="text-6xl sm:text-7xl">🔥</span>
                        <div class="text-left">
                            <div class="font-serif text-5xl sm:text-6xl font-black text-stone-900 leading-none">{{ $currentStreak }}</div>
                            <div class="text-sm font-semibold text-stone-500 mt-1">{{ Str::plural('consecutive day', $currentStreak) }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-stone-100">
                    @if($todayRead)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold">
                            <span>✓ Reading recorded today. Streak secured!</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold">
                            <span>⏳ Read 1+ page today before midnight to maintain your streak.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Longest Streak Card -->
            <div class="p-8 rounded-3xl bg-white border border-stone-200/80 shadow-xs text-center flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Longest Streak Achieved</span>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <span class="text-6xl sm:text-7xl">🏆</span>
                        <div class="text-left">
                            <div class="font-serif text-5xl sm:text-6xl font-black text-stone-900 leading-none">{{ $longestStreak }}</div>
                            <div class="text-sm font-semibold text-stone-500 mt-1">{{ Str::plural('day', $longestStreak) }} record</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-stone-100 text-xs text-stone-500">
                    System Timezone: <strong class="text-stone-800">{{ $appTimezone }}</strong>
                </div>
            </div>
        </div>

        <!-- 30-Day Activity Matrix Grid -->
        <div class="max-w-4xl mx-auto bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 pb-4 border-b border-stone-100">
                <div>
                    <h2 class="font-serif text-lg font-bold text-stone-900">Recent 30-Day Calendar Map</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Green squares indicate verified reading days (>0 pages read)</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-stone-500">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-stone-100 border border-stone-200"></span>
                        <span>0 pages</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-emerald-600"></span>
                        <span>Active Day</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-6 sm:grid-cols-10 md:grid-cols-15 gap-2">
                @foreach($activityMap as $day)
                    <div class="p-2 rounded-xl text-center border flex flex-col items-center justify-between aspect-square transition {{ $day['is_active'] ? 'bg-emerald-500 text-white border-emerald-600 shadow-2xs' : 'bg-stone-50 text-stone-700 border-stone-200' }} {{ $day['is_today'] ? 'ring-2 ring-stone-900 ring-offset-2' : '' }}">
                        <span class="text-[9px] uppercase font-bold {{ $day['is_active'] ? 'text-emerald-100' : 'text-stone-400' }}">{{ $day['day_name'] }}</span>
                        <span class="text-xs font-bold leading-none">{{ $day['day_number'] }}</span>
                        <span class="text-[9px] font-medium leading-none {{ $day['is_active'] ? 'text-emerald-100' : 'text-stone-400' }}">{{ $day['pages'] }}p</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Official SRS Business Rules Explanation (SRS FR-050 to FR-055) -->
        <div class="max-w-4xl mx-auto bg-stone-100 p-6 sm:p-8 rounded-3xl border border-stone-200 text-stone-800">
            <h3 class="font-serif font-bold text-base text-stone-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>SRS Reading Streak Business Rules (Specification Compliance)</span>
            </h3>
            <ul class="text-xs space-y-2 text-stone-700 leading-relaxed list-disc list-inside">
                <li><strong class="text-stone-900">FR-050:</strong> Reading more than zero pages (>0) on a calendar day qualifies as a reading day.</li>
                <li><strong class="text-stone-900">FR-051:</strong> The daily page goal (e.g., 20 pages) does <em>not</em> have to be met for the day to qualify toward the streak.</li>
                <li><strong class="text-stone-900">FR-052:</strong> A day with zero pages is not a reading day.</li>
                <li><strong class="text-stone-900">FR-053:</strong> A missing calendar day breaks the current consecutive-day streak.</li>
                <li><strong class="text-stone-900">FR-054:</strong> Streak calculations use a single documented timezone/date policy ({{ $appTimezone }}).</li>
                <li><strong class="text-stone-900">FR-055:</strong> Duplicate log submissions within the same day are summed together and never multiply the streak count.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
