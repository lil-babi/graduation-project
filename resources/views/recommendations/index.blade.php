@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-100 text-brand-900 border border-brand-200 mb-3">
                <span>Rule-Based Catalog Curation</span>
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900">Curated For You</h1>
            <p class="text-sm text-stone-600 mt-2">
                Personalized book discoveries derived from the genres in your library, your reader profile preferences, and community favorites.
            </p>
            <div class="mt-3 text-[11px] text-stone-400">
                Notice (FR-083): Recommendations are generated via deterministic category affinity and reading history matching.
            </div>
        </div>

        @if(count($recommendations) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recommendations as $rec)
                    @php $book = $rec['book']; @endphp
                    <div class="bg-white rounded-3xl border border-stone-200/80 p-5 shadow-xs flex flex-col justify-between group hover:shadow-md transition">
                        <div>
                            <!-- Reason Tag (SRS FR-082) -->
                            <div class="mb-4 flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-50 text-brand-900 border border-brand-200">
                                    {{ $rec['badge'] }}
                                </span>
                                <span class="text-[11px] text-stone-400">{{ $book->category->name }}</span>
                            </div>

                            <div class="flex gap-4 mb-4">
                                <a href="{{ route('books.show', $book) }}" class="w-20 h-28 shrink-0 bg-stone-100 rounded-xl overflow-hidden border border-stone-200 block">
                                    @if($book->cover_path)
                                        <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full p-2 flex flex-col justify-between bg-stone-800 text-white text-[9px]">
                                            <span class="font-bold line-clamp-2">{{ $book->title }}</span>
                                            <span class="text-stone-400 truncate">{{ $book->author }}</span>
                                        </div>
                                    @endif
                                </a>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-serif font-bold text-sm text-stone-900 group-hover:text-brand-700 transition">
                                        <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                    </h3>
                                    <p class="text-xs text-stone-500 mt-0.5 truncate">{{ $book->author }}</p>
                                    <div class="mt-2 text-xs text-stone-500">
                                        {{ $book->page_count }} pages • {{ $book->publication_year }}
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-500 text-xs font-semibold mt-1">
                                        <span>{{ $book->averageRating() > 0 ? $book->averageRating() . ' ★' : 'Unrated' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Human-readable Explanation (SRS FR-082) -->
                            <div class="p-3 rounded-xl bg-stone-50 border border-stone-100 text-xs text-stone-600 font-serif italic leading-relaxed">
                                “{{ $rec['reason'] }}”
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between">
                            <a href="{{ route('books.show', $book) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900">
                                View Details & Reviews →
                            </a>

                            @auth
                                @php $status = $book->userStatus(auth()->user()); @endphp
                                @if($status)
                                    <span class="text-[11px] font-bold text-emerald-700">✓ In Library</span>
                                @else
                                    <form method="POST" action="{{ route('library.store') }}">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-stone-900 text-white hover:bg-stone-800 transition">
                                            + Add
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center bg-white rounded-3xl border border-stone-200 text-xs text-stone-500 max-w-md mx-auto">
                No recommendations found yet. Save a few books to your library to see personalized recommendations!
            </div>
        @endif
    </div>
</div>
@endsection
