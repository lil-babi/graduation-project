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
                <h1 class="font-serif text-3xl font-bold text-stone-900">Personalized Reading Plans</h1>
                <p class="text-sm text-stone-600 mt-1">Set target completion deadlines for any book and follow a tailored daily page pace.</p>
            </div>
            <a href="{{ route('premium.analytics') }}" class="px-3.5 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold transition">
                ← Back to Analytics
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Create Reading Plan Form -->
            <div class="lg:col-span-4">
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs sticky top-24">
                    <h2 class="font-serif text-lg font-bold text-stone-900 mb-1">Create Reading Plan</h2>
                    <p class="text-xs text-stone-500 mb-5">Select a book from your library and set your goal completion date.</p>

                    <form method="POST" action="{{ route('premium.plans.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="book_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Target Book</label>
                            <select id="book_id" name="book_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                                <option value="">-- Choose a book from your library --</option>
                                @foreach($availableBooks as $item)
                                    <option value="{{ $item->book->id }}">
                                        {{ $item->book->title }} ({{ $item->book->page_count }} pages)
                                    </option>
                                @endforeach
                            </select>
                            @if($availableBooks->isEmpty())
                                <span class="text-[11px] text-amber-700 mt-1 block">
                                    You don't have any books in your library. Add books in <a href="{{ route('books.index') }}" class="underline font-bold">Discover</a> first.
                                </span>
                            @endif
                        </div>

                        <div>
                            <label for="target_completion_date" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Target Completion Date</label>
                            <input id="target_completion_date" type="date" name="target_completion_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Notes / Intentions (Optional)</label>
                            <textarea id="notes" name="notes" rows="2" placeholder="e.g. Read during morning coffee session"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                            Generate Personalized Pace
                        </button>
                    </form>

                    <!-- Upcoming Plans Capabilities -->
                    <div class="mt-6 p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs">
                        <div class="flex items-center gap-1.5 text-amber-900 font-bold mb-1.5">
                            <span>🚀 Coming Soon to Plans</span>
                        </div>
                        <ul class="space-y-1.5 text-[11px] text-amber-800">
                            <li class="flex items-start gap-1.5">
                                <span class="font-bold">✦</span>
                                <span><strong>Calendar Sync:</strong> One-click export of your daily page pace to Google/Apple Calendar.</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="font-bold">✦</span>
                                <span><strong>Adaptive Catch-up:</strong> Dynamically recalculate your daily target if you fall behind.</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="font-bold">✦</span>
                                <span><strong>Smart Streak Alerts:</strong> Gentle notifications before midnight to protect your active streak.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right: Active & Historical Plans -->
            <div class="lg:col-span-8">
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-stone-900 mb-6 pb-3 border-b border-stone-100">
                        Your Reading Schedules
                    </h2>

                    @if($readingPlans->isNotEmpty())
                        <div class="space-y-6">
                            @foreach($readingPlans as $plan)
                                @php $book = $plan->book; @endphp
                                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200/80 space-y-4">
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-700">{{ $book->category->name }}</span>
                                            <h3 class="font-serif font-bold text-base text-stone-900">{{ $book->title }}</h3>
                                            <p class="text-xs text-stone-500">{{ $book->author }} • {{ $book->page_count }} pages</p>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $plan->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($plan->status === 'abandoned' ? 'bg-stone-200 text-stone-600' : 'bg-amber-100 text-amber-900') }}">
                                                {{ ucfirst($plan->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Plan target pace box -->
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-xl bg-white border border-stone-200/60 text-xs">
                                        <div>
                                            <span class="text-stone-400 block text-[10px] uppercase font-semibold">Required Pace</span>
                                            <span class="font-serif font-bold text-lg text-stone-900">{{ $plan->daily_target_pages }}</span>
                                            <span class="text-stone-500">pages / day</span>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 block text-[10px] uppercase font-semibold">Deadline</span>
                                            <span class="font-bold text-stone-900">{{ $plan->target_completion_date->format('M d, Y') }}</span>
                                            <span class="text-stone-500 block text-[10px]">({{ $plan->target_completion_date->diffForHumans() }})</span>
                                        </div>
                                        <div>
                                            <span class="text-stone-400 block text-[10px] uppercase font-semibold">User Progress</span>
                                            <span class="font-bold text-stone-900">{{ $book->userCurrentPage(auth()->user()) }} / {{ $book->page_count }}</span>
                                            <span class="text-stone-500 block text-[10px]">{{ $book->userProgressPercent(auth()->user()) }}%</span>
                                        </div>
                                    </div>

                                    @if($plan->notes)
                                        <p class="text-xs text-stone-600 italic">“{{ $plan->notes }}”</p>
                                    @endif

                                    <!-- Actions -->
                                    <div class="pt-3 border-t border-stone-200/60 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            @if($plan->status === 'active')
                                                <form method="POST" action="{{ route('premium.plans.update', $plan) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="text-emerald-700 font-semibold hover:underline">
                                                        Mark Completed ✓
                                                    </button>
                                                </form>

                                                <span class="text-stone-300">•</span>

                                                <form method="POST" action="{{ route('premium.plans.update', $plan) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="abandoned">
                                                    <button type="submit" class="text-stone-500 hover:underline">
                                                        Archive Plan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('premium.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this reading plan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-[11px]">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-xs text-stone-500">
                            No personalized reading plans created yet. Use the tool on the left to set a deadline for any book in your library!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
