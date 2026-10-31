@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">My Personal Library</h1>
                <p class="text-sm text-stone-600 mt-1">Organize your saved titles, track reading statuses, and update page progress.</p>
            </div>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                <span>+ Discover More Books</span>
            </a>
        </div>

        <!-- Filter Tabs (SRS FR-031 & FR-032) -->
        <div class="flex items-center gap-2 border-b border-stone-200 pb-3 mb-8 overflow-x-auto text-xs font-semibold">
            <a href="{{ route('library.index', ['status' => 'all']) }}"
                class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ $status === 'all' ? 'bg-stone-900 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                <span>All Saved</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'all' ? 'bg-stone-800 text-stone-200' : 'bg-stone-200 text-stone-700' }}">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('library.index', ['status' => 'reading']) }}"
                class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ $status === 'reading' ? 'bg-blue-600 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                <span>Currently Reading</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'reading' ? 'bg-blue-700 text-blue-100' : 'bg-stone-200 text-stone-700' }}">{{ $counts['reading'] }}</span>
            </a>
            <a href="{{ route('library.index', ['status' => 'want_to_read']) }}"
                class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ $status === 'want_to_read' ? 'bg-stone-900 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                <span>Want to Read</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'want_to_read' ? 'bg-stone-800 text-stone-200' : 'bg-stone-200 text-stone-700' }}">{{ $counts['want_to_read'] }}</span>
            </a>
            <a href="{{ route('library.index', ['status' => 'completed']) }}"
                class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ $status === 'completed' ? 'bg-emerald-700 text-white shadow-xs' : 'text-stone-600 hover:bg-stone-100' }}">
                <span>Completed</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'completed' ? 'bg-emerald-800 text-emerald-100' : 'bg-stone-200 text-stone-700' }}">{{ $counts['completed'] }}</span>
            </a>
        </div>

        <!-- Library Items Grid -->
        @if($userBooks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($userBooks as $item)
                    @php $book = $item->book; @endphp
                    <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex gap-4">
                                <!-- Cover thumbnail -->
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
                                    <span class="text-[10px] font-semibold text-brand-700 uppercase tracking-wider">{{ $book->category->name }}</span>
                                    <h3 class="font-serif font-bold text-sm text-stone-900 mt-0.5 truncate hover:text-brand-700">
                                        <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                    </h3>
                                    <p class="text-xs text-stone-500 truncate">{{ $book->author }}</p>

                                    <!-- Status Pill -->
                                    <div class="mt-2">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $item->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'reading' ? 'bg-blue-100 text-blue-800' : 'bg-stone-100 text-stone-800') }}">
                                            {{ ucwords(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar & Page Info -->
                            <div class="mt-4 pt-3 border-t border-stone-100">
                                <div class="flex justify-between text-xs text-stone-600 mb-1">
                                    <span>{{ $item->current_page }} / {{ $book->page_count }} pages</span>
                                    <span class="font-bold text-stone-900">{{ $book->userProgressPercent(auth()->user()) }}%</span>
                                </div>
                                <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-stone-900 h-2 rounded-full transition-all" style="width: {{ $book->userProgressPercent(auth()->user()) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Form & Actions (SRS FR-033) -->
                        <div class="mt-5 pt-3 border-t border-stone-100 space-y-3">
                            <form method="POST" action="{{ route('library.update', $item) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')

                                <select name="status" class="flex-1 text-xs px-2.5 py-1.5 rounded-lg border border-stone-300 bg-white focus:outline-none">
                                    <option value="want_to_read" {{ $item->status === 'want_to_read' ? 'selected' : '' }}>Want to Read</option>
                                    <option value="reading" {{ $item->status === 'reading' ? 'selected' : '' }}>Reading</option>
                                    <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>

                                <input type="number" name="current_page" value="{{ $item->current_page }}" min="0" max="{{ $book->page_count }}"
                                    class="w-16 text-xs px-2 py-1.5 rounded-lg border border-stone-300 bg-white text-center focus:outline-none" title="Current Page">

                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-stone-900 text-white rounded-lg hover:bg-stone-800 transition">
                                    Save
                                </button>
                            </form>

                            <div class="flex items-center justify-between pt-1 text-xs">
                                @if($book->pdf_path)
                                    <a href="{{ route('books.read', $book) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        <span>Read PDF</span>
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                <form method="POST" action="{{ route('library.destroy', $item) }}" onsubmit="return confirm('Remove from library?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-stone-400 hover:text-red-600 transition">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $userBooks->links() }}
            </div>
        @else
            <!-- Empty Library State -->
            <div class="text-center py-16 bg-white rounded-2xl border border-stone-200/80 p-8 max-w-md mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-stone-100 text-stone-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-serif font-bold text-lg text-stone-900">Your library is empty</h3>
                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                    You haven't added any books to your personal library yet. Explore our curated catalog and start building your collection.
                </p>
                <div class="mt-5">
                    <a href="{{ route('books.index') }}" class="inline-flex px-4 py-2.5 rounded-xl text-xs font-semibold bg-stone-900 text-white hover:bg-stone-800 transition">
                        Explore Book Catalog
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
