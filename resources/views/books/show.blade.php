@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-500 mb-6">
            <a href="{{ route('books.index') }}" class="hover:text-stone-900">Catalog</a>
            <span>/</span>
            <a href="{{ route('books.index', ['category' => $book->category->slug]) }}" class="hover:text-stone-900">{{ $book->category->name }}</a>
            <span>/</span>
            <span class="text-stone-900 font-medium truncate">{{ $book->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Column: Cover & Actions -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    <div class="aspect-[3/4] bg-stone-100 rounded-2xl border border-stone-200/80 overflow-hidden shadow-sm">
                        @if($book->cover_path)
                            <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full p-8 flex flex-col justify-between bg-gradient-to-br from-stone-800 to-stone-900 text-white">
                                <span class="text-xs uppercase font-bold tracking-wider text-stone-400">{{ $book->category->name }}</span>
                                <div>
                                    <h2 class="font-serif font-bold text-2xl leading-tight">{{ $book->title }}</h2>
                                    <p class="text-sm text-stone-300 mt-2">{{ $book->author }}</p>
                                </div>
                                <div class="text-xs text-stone-400">{{ $book->publication_year }} • {{ $book->page_count }} Pages</div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions Card -->
                    <div class="bg-white p-6 rounded-2xl border border-stone-200/80 shadow-xs space-y-4">
                        <!-- Digital PDF Reader Action (SRS FR-035 & FR-090) -->
                        @if($book->pdf_path)
                            <a href="{{ route('books.read', $book) }}" class="w-full py-3 px-4 rounded-xl bg-brand-700 text-white font-semibold text-sm hover:bg-brand-800 transition flex items-center justify-center gap-2 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Read Book / Open PDF</span>
                            </a>
                        @endif

                        @auth
                            @if($userBook)
                                <!-- In Library State -->
                                <div class="p-4 rounded-xl bg-stone-50 border border-stone-200 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">In Your Library</span>
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $userBook->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($userBook->status === 'reading' ? 'bg-blue-100 text-blue-800' : 'bg-stone-200 text-stone-800') }}">
                                            {{ ucwords(str_replace('_', ' ', $userBook->status)) }}
                                        </span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div>
                                        <div class="flex justify-between text-xs text-stone-600 mb-1">
                                            <span>Progress: {{ $userBook->current_page }} / {{ $book->page_count }} pages</span>
                                            <span class="font-bold">{{ $book->userProgressPercent(auth()->user()) }}%</span>
                                        </div>
                                        <div class="w-full bg-stone-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-stone-900 h-2 rounded-full transition-all duration-300" style="width: {{ $book->userProgressPercent(auth()->user()) }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Quick Status & Page Update Form -->
                                    <form method="POST" action="{{ route('library.update', $userBook) }}" class="space-y-3 pt-2">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-xs font-medium text-stone-600 mb-1">Update Status</label>
                                            <select name="status" class="w-full text-xs px-3 py-2 rounded-lg border border-stone-300 bg-white focus:outline-none focus:ring-1 focus:ring-stone-900">
                                                <option value="want_to_read" {{ $userBook->status === 'want_to_read' ? 'selected' : '' }}>Want to Read</option>
                                                <option value="reading" {{ $userBook->status === 'reading' ? 'selected' : '' }}>Currently Reading</option>
                                                <option value="completed" {{ $userBook->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-stone-600 mb-1">Current Page</label>
                                            <input type="number" name="current_page" value="{{ $userBook->current_page }}" min="0" max="{{ $book->page_count }}"
                                                class="w-full text-xs px-3 py-2 rounded-lg border border-stone-300 bg-white focus:outline-none focus:ring-1 focus:ring-stone-900">
                                        </div>

                                        <button type="submit" class="w-full py-2 text-xs font-semibold bg-stone-900 text-white rounded-lg hover:bg-stone-800 transition">
                                            Save Status & Progress
                                        </button>
                                    </form>

                                    <!-- Remove Button -->
                                    <form method="POST" action="{{ route('library.destroy', $userBook) }}" class="text-center pt-1" onsubmit="return confirm('Remove this book from your library?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline">
                                            Remove from Library
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- Add to Library Button -->
                                <form method="POST" action="{{ route('library.store') }}">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <input type="hidden" name="status" value="want_to_read">
                                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-stone-800 transition shadow-xs flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        <span>Add to My Books</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-sm hover:bg-stone-800 transition shadow-xs block text-center">
                                Sign in to Add to Library
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Right Column: Details, Description, Reviews -->
            <div class="lg:col-span-8 space-y-10">
                <!-- Book Header -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-stone-100 text-stone-800">{{ $book->category->name }}</span>
                        <span class="text-xs text-stone-400">•</span>
                        <span class="text-xs text-stone-500">Published {{ $book->publication_year }}</span>
                        <span class="text-xs text-stone-400">•</span>
                        <span class="text-xs text-stone-500">{{ $book->page_count }} Pages</span>
                    </div>

                    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900 tracking-tight">{{ $book->title }}</h1>
                    <p class="text-base text-stone-600 mt-2 font-medium">By <span class="text-stone-900">{{ $book->author }}</span></p>

                    <!-- Rating Summary -->
                    <div class="flex items-center gap-3 mt-4">
                        <div class="flex items-center gap-1 text-amber-500">
                            @php $avgRating = $book->averageRating(); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-amber-500' : 'text-stone-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-stone-900 text-sm">{{ $avgRating > 0 ? $avgRating . ' / 5.0' : 'No ratings yet' }}</span>
                        <span class="text-xs text-stone-500">({{ $book->reviewsCount() }} {{ Str::plural('review', $book->reviewsCount()) }})</span>
                    </div>
                </div>

                <!-- Synopsis / Description -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-xl font-bold text-stone-900 mb-3">Synopsis & Overview</h2>
                    <div class="text-sm text-stone-700 leading-relaxed space-y-4 whitespace-pre-line font-serif">
                        {{ $book->description }}
                    </div>
                </div>

                <!-- Quotes from this Book -->
                @if($book->quotes->isNotEmpty())
                    <div class="bg-stone-50 p-6 rounded-2xl border border-stone-200/80">
                        <h2 class="font-serif text-lg font-bold text-stone-900 mb-4 flex items-center gap-2">
                            <span>Notable Quotes</span>
                            <span class="text-xs font-normal text-stone-500">({{ $book->quotes->count() }})</span>
                        </h2>
                        <div class="space-y-4">
                            @foreach($book->quotes as $quote)
                                <div class="bg-white p-4 rounded-xl border border-stone-200/60 shadow-2xs">
                                    <p class="font-serif italic text-sm text-stone-800">“{{ $quote->text }}”</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviews & Ratings Section (SRS FR-060 to FR-065) -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/80 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                        <div>
                            <h2 class="font-serif text-xl font-bold text-stone-900">Reader Reviews</h2>
                            <p class="text-xs text-stone-500 mt-0.5">Thoughts and reflections from readers on BookPlatform</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-stone-100 text-stone-700">
                            {{ $book->reviewsCount() }} total
                        </span>
                    </div>

                    <!-- Submit Review Form -->
                    @auth
                        <div class="p-5 rounded-xl bg-stone-50 border border-stone-200">
                            <h3 class="text-sm font-semibold text-stone-900 mb-2">
                                {{ $userReview ? 'Update Your Review' : 'Write a Review' }}
                            </h3>
                            <form method="POST" action="{{ route('reviews.store', $book) }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-medium text-stone-700 mb-1">Your Rating (1 to 5 Stars)</label>
                                    <div class="flex items-center gap-3">
                                        @for($star = 5; $star >= 1; $star--)
                                            <label class="flex items-center gap-1 cursor-pointer text-xs font-semibold text-stone-700">
                                                <input type="radio" name="rating" value="{{ $star }}" {{ ($userReview && $userReview->rating === $star) || (!$userReview && $star === 5) ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                                <span>{{ $star }} ★</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div>
                                    <label for="review_body" class="block text-xs font-medium text-stone-700 mb-1">Your Review</label>
                                    <textarea id="review_body" name="body" rows="3" required placeholder="What did you learn from this book? Who would you recommend it to?"
                                        class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 focus:border-stone-900 bg-white">{{ old('body', $userReview?->body) }}</textarea>
                                </div>

                                <button type="submit" class="py-2.5 px-4 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition">
                                    {{ $userReview ? 'Update Review' : 'Post Review' }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-stone-50 text-center text-xs text-stone-600">
                            <a href="{{ route('login') }}" class="font-semibold text-stone-900 hover:underline">Log in</a> to write a review and rate this book.
                        </div>
                    @endauth

                    <!-- Reviews List -->
                    <div class="space-y-4">
                        @forelse($book->reviews as $rev)
                            <div class="p-4 rounded-xl border border-stone-200/70 bg-stone-50/50">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-brand-200 text-brand-900 font-bold text-xs flex items-center justify-center">
                                            {{ substr($rev->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-stone-900">{{ $rev->user->name }}</div>
                                            <div class="text-[10px] text-stone-500">{{ $rev->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-0.5 text-amber-500 text-xs font-bold">
                                        <span>{{ $rev->rating }}</span>
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-stone-700 leading-relaxed font-serif whitespace-pre-line">{{ $rev->body }}</p>

                                @if(auth()->check() && (auth()->id() === $rev->user_id || auth()->user()->isAdmin()))
                                    <div class="mt-2 pt-2 border-t border-stone-200/50 flex justify-end">
                                        <form method="POST" action="{{ route('reviews.destroy', $rev) }}" onsubmit="return confirm('Delete this review?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[11px] text-red-600 hover:underline">Delete Review</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center py-6 text-xs text-stone-500">No reviews yet. Be the first reader to review this book!</p>
                        @endforelse
                    </div>
                </div>

                <!-- Related Books -->
                @if($relatedBooks->isNotEmpty())
                    <div>
                        <h2 class="font-serif text-xl font-bold text-stone-900 mb-4">More in {{ $book->category->name }}</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach($relatedBooks as $rel)
                                <a href="{{ route('books.show', $rel) }}" class="p-3 bg-white rounded-xl border border-stone-200/70 hover:shadow-xs transition block group">
                                    <div class="aspect-[3/4] bg-stone-100 rounded-lg overflow-hidden mb-2">
                                        @if($rel->cover_path)
                                            <img src="{{ asset('storage/' . $rel->cover_path) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full p-2 flex flex-col justify-between bg-stone-800 text-white text-[10px]">
                                                <span class="line-clamp-2 font-serif font-bold">{{ $rel->title }}</span>
                                                <span class="text-stone-400 truncate">{{ $rel->author }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="font-serif font-bold text-xs text-stone-900 line-clamp-1 group-hover:text-brand-700">{{ $rel->title }}</h4>
                                    <p class="text-[11px] text-stone-500 truncate">{{ $rel->author }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
