@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">Book Catalog & Discovery</h1>
                <p class="text-sm text-stone-600 mt-1">Browse, search, and discover books to add to your personal reading library.</p>
            </div>

            <!-- Search Bar (SRS FR-021) -->
            <form method="GET" action="{{ route('books.index') }}" class="flex items-center gap-2 max-w-md w-full">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by title, author, or keyword..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900 bg-white">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-stone-800 transition">
                    Search
                </button>
                @if($search)
                    <a href="{{ route('books.index', ['category' => request('category')]) }}" class="p-2.5 text-stone-400 hover:text-stone-700 rounded-xl hover:bg-stone-200 transition" title="Clear Search">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        <!-- Category Filter Pills (SRS FR-022 & FR-024) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 text-xs scrollbar-none">
            <a href="{{ route('books.index', ['search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-full font-medium whitespace-nowrap transition {{ $categorySlug === 'all' || empty($categorySlug) ? 'bg-stone-900 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('books.index', ['category' => $cat->slug, 'search' => $search]) }}"
                    class="px-3.5 py-1.5 rounded-full font-medium whitespace-nowrap transition {{ $categorySlug === $cat->slug ? 'bg-stone-900 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                    {{ $cat->name }} ({{ $cat->books_count }})
                </a>
            @endforeach
        </div>

        <!-- Books Grid -->
        @if($books->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($books as $book)
                    <div class="bg-white rounded-2xl border border-stone-200/80 overflow-hidden flex flex-col justify-between hover:shadow-md transition group">
                        <div>
                            <!-- Cover -->
                            <a href="{{ route('books.show', $book) }}" class="relative aspect-[3/4] bg-stone-100 block overflow-hidden">
                                @if($book->cover_path)
                                    <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full p-6 flex flex-col justify-between bg-gradient-to-br from-stone-800 to-stone-900 text-white">
                                        <div class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">{{ $book->category->name }}</div>
                                        <div>
                                            <h3 class="font-serif font-bold text-base leading-snug line-clamp-3">{{ $book->title }}</h3>
                                            <p class="text-xs text-stone-300 mt-1">{{ $book->author }}</p>
                                        </div>
                                        <div class="text-[10px] text-stone-400">{{ $book->publication_year }} • {{ $book->page_count }} pages</div>
                                    </div>
                                @endif

                                @if($book->pdf_path)
                                    <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-stone-900/80 text-white backdrop-blur flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        PDF
                                    </span>
                                @endif
                            </a>

                            <!-- Info -->
                            <div class="p-4">
                                <span class="text-[10px] font-semibold text-brand-700 uppercase tracking-wider">{{ $book->category->name }}</span>
                                <h3 class="font-serif font-bold text-base text-stone-900 mt-1 line-clamp-1 group-hover:text-brand-700 transition">
                                    <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                </h3>
                                <p class="text-xs text-stone-600 mt-0.5">{{ $book->author }} ({{ $book->publication_year }})</p>
                                <p class="text-xs text-stone-500 mt-2 line-clamp-2 leading-relaxed">{{ $book->description }}</p>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="p-4 pt-0">
                            <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-xs mb-3">
                                <span class="text-stone-500">{{ $book->page_count }} pages</span>
                                <div class="flex items-center gap-1 text-amber-600 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <span>{{ $book->averageRating() > 0 ? $book->averageRating() : 'No reviews' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('books.show', $book) }}" class="flex-1 py-2 text-center text-xs font-semibold text-stone-700 bg-stone-100 hover:bg-stone-200 rounded-xl transition">
                                    Details
                                </a>

                                @auth
                                    @php $status = $book->userStatus(auth()->user()); @endphp
                                    @if($status)
                                        <span class="px-2.5 py-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-1">
                                            ✓ {{ ucwords(str_replace('_', ' ', $status)) }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('library.store') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <input type="hidden" name="status" value="want_to_read">
                                            <button type="submit" class="px-3 py-2 text-xs font-semibold text-white bg-stone-900 hover:bg-stone-800 rounded-xl transition" title="Add to My Books">
                                                + Library
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $books->links() }}
            </div>
        @else
            <!-- Empty State (SRS FR-026) -->
            <div class="text-center py-16 bg-white rounded-2xl border border-stone-200/80 p-8 max-w-lg mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-stone-100 text-stone-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-serif font-bold text-lg text-stone-900">No books found</h3>
                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                    We couldn't find any catalog entries matching your criteria. Try adjusting your search term or selecting a different category.
                </p>
                <div class="mt-4">
                    <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-stone-900 text-white hover:bg-stone-800 transition">
                        Reset Filters
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
