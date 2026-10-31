@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden">
    <!-- Hero Section -->
    <div class="relative pt-12 pb-20 md:pt-20 md:pb-28 bg-gradient-to-b from-brand-50/60 to-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <!-- Tagline Badge (SRS FR-002) -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-100 text-brand-900 border border-brand-200 text-xs font-semibold mb-6">
                    <svg class="w-4 h-4 text-brand-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                    <span>“Read today, lead tomorrow.”</span>
                </div>

                <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-stone-900 tracking-tight leading-[1.15]">
                    Your Personal Sanctuary for Reading & Habit Growth
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-stone-600 font-normal leading-relaxed max-w-2xl mx-auto">
                    Discover inspiring books, track your daily page goals, maintain reading streaks, and read authorized digital books — all in one focused platform.
                </p>

                <!-- CTA buttons -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('books.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-brand-700 transition shadow-sm">
                        <span>Browse Books Catalog</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                    @guest
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white border border-stone-300 text-stone-800 font-semibold text-sm hover:bg-stone-50 transition shadow-xs">
                            Create Free Account
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white border border-stone-300 text-stone-800 font-semibold text-sm hover:bg-stone-50 transition shadow-xs">
                            Open Your Dashboard
                        </a>
                    @endguest
                </div>

                <!-- Demonstration notice pill -->
                <div class="mt-6 text-xs text-stone-500 flex items-center justify-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>INSA Graduation Project • Complete Laravel & Blade Implementation</span>
                </div>
            </div>

            <!-- Platform Stats Banner -->
            <div class="mt-14 max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-4 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                    <div class="font-serif text-2xl md:text-3xl font-bold text-stone-900">{{ $stats['total_books'] }}</div>
                    <div class="text-xs font-medium text-stone-500 mt-1">Curated Books</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                    <div class="font-serif text-2xl md:text-3xl font-bold text-stone-900">{{ $stats['categories_count'] }}</div>
                    <div class="text-xs font-medium text-stone-500 mt-1">Genres & Categories</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                    <div class="font-serif text-2xl md:text-3xl font-bold text-stone-900">{{ number_format($stats['total_pages_logged']) }}</div>
                    <div class="text-xs font-medium text-stone-500 mt-1">Pages Read</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-stone-200/80 shadow-xs">
                    <div class="font-serif text-2xl md:text-3xl font-bold text-stone-900">99 ETB</div>
                    <div class="text-xs font-medium text-stone-500 mt-1">Premium / Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Books Section -->
    <div class="py-16 bg-white border-y border-stone-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                <div>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">Featured in Catalog</h2>
                    <p class="mt-2 text-sm text-stone-600">Discover essential classics and foundational literature available to read.</p>
                </div>
                <a href="{{ route('books.index') }}" class="mt-4 md:mt-0 text-sm font-semibold text-brand-700 hover:text-brand-900 inline-flex items-center gap-1">
                    <span>View all {{ $stats['total_books'] }} books</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @forelse($featuredBooks as $book)
                    <div class="group flex flex-col bg-stone-50 rounded-2xl border border-stone-200/80 overflow-hidden hover:shadow-md transition">
                        <!-- Book Cover Placeholder / Image -->
                        <a href="{{ route('books.show', $book) }}" class="relative aspect-[3/4] bg-stone-200 flex items-center justify-center overflow-hidden">
                            @if($book->cover_path)
                                <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full p-4 flex flex-col justify-between bg-gradient-to-br from-stone-800 to-stone-900 text-stone-100">
                                    <div class="text-[10px] font-semibold tracking-wider uppercase text-stone-400">{{ $book->category->name }}</div>
                                    <div class="font-serif font-bold text-sm leading-snug line-clamp-3">{{ $book->title }}</div>
                                    <div class="text-[11px] text-stone-300 truncate">{{ $book->author }}</div>
                                </div>
                            @endif

                            @if($book->pdf_path)
                                <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-stone-900/80 text-white backdrop-blur">PDF</span>
                            @endif
                        </a>

                        <div class="p-3.5 flex flex-col flex-1 justify-between">
                            <div>
                                <span class="text-[10px] font-medium text-brand-700 uppercase tracking-wider">{{ $book->category->name }}</span>
                                <h3 class="font-serif font-bold text-sm text-stone-900 mt-0.5 line-clamp-1 group-hover:text-brand-700 transition">
                                    <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                </h3>
                                <p class="text-xs text-stone-500 mt-0.5 truncate">{{ $book->author }}</p>
                            </div>

                            <div class="mt-3 pt-2 border-t border-stone-200/60 flex items-center justify-between text-xs text-stone-500">
                                <span>{{ $book->page_count }} pages</span>
                                <div class="flex items-center gap-1 text-amber-600 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <span>{{ $book->averageRating() > 0 ? $book->averageRating() : '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-stone-500">
                        No books available yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Core Value Propositions / Features Grid -->
    <div class="py-20 bg-stone-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="font-serif text-3xl font-bold text-stone-900">Engineered for Habitual Readers</h2>
                <p class="mt-3 text-sm text-stone-600 leading-relaxed">
                    Designed to eliminate clutter and provide genuine reading continuity, strict daily streaks, and seamless personal library management.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-stone-100 text-stone-900 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-stone-900">Consecutive Reading Streaks</h3>
                        <p class="mt-2 text-sm text-stone-600 leading-relaxed">
                            Reading at least 1 page each calendar day preserves your active reading streak. Clear rules ensure honest progress tracking.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-stone-100 text-xs font-semibold text-brand-700">
                        Zero pages breaks the streak • 1 documented timezone policy
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-stone-100 text-stone-900 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-stone-900">Authorized Digital Reader</h3>
                        <p class="mt-2 text-sm text-stone-600 leading-relaxed">
                            Read open-licensed PDF editions directly in the application with quick page progress logging and responsive page navigation.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-stone-100 text-xs font-semibold text-brand-700">
                        Built-in viewer • Safe file handling
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-800 border border-amber-200/60 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-stone-900">Freemium Business Model</h3>
                        <p class="mt-2 text-sm text-stone-600 leading-relaxed">
                            Complete access to the core library and streaks on the Free plan. Upgrade to Premium (99 ETB/mo) for deep velocity analytics and personalized reading plans.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-stone-100 text-xs font-semibold text-amber-700">
                        Simulated demonstration checkout • Transparent audit reports
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Quotes Showcase -->
    @if($sampleQuotes->isNotEmpty())
        <div class="py-16 bg-white border-t border-stone-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <span class="text-xs uppercase font-semibold text-brand-700 tracking-wider">Reader Wisdom</span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900 mt-1">Highlighted Quotes</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($sampleQuotes as $quote)
                        <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200/70 flex flex-col justify-between">
                            <div class="text-stone-700 font-serif italic text-sm leading-relaxed">
                                “{{ $quote->text }}”
                            </div>
                            <div class="mt-4 pt-3 border-t border-stone-200/60 text-xs text-stone-500 flex items-center justify-between">
                                <span class="font-medium text-stone-800">{{ $quote->source ?? ($quote->book ? $quote->book->title : 'Unknown') }}</span>
                                <span>Saved by {{ $quote->user->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Call to Action Banner -->
    <div class="py-16 bg-stone-900 text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-serif text-3xl sm:text-4xl font-bold">Start your reading journey today</h2>
            <p class="mt-4 text-stone-400 max-w-xl mx-auto text-sm sm:text-base">
                Join readers tracking books, reaching page goals, and reading authorized open literature on BookPlatform.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('books.index') }}" class="px-6 py-3 rounded-xl bg-white text-stone-900 font-semibold text-sm hover:bg-stone-100 transition">
                    Explore the Catalog
                </a>
                <a href="{{ route('pricing') }}" class="px-6 py-3 rounded-xl bg-stone-800 border border-stone-700 text-white font-semibold text-sm hover:bg-stone-700 transition">
                    View Pricing (99 ETB/mo)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
