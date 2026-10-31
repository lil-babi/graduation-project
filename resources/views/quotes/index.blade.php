@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-stone-900">Personal Quotes & Highlights</h1>
                <p class="text-sm text-stone-600 mt-1">Capture resonant ideas, timeless reflections, and memorable excerpts.</p>
            </div>

            <!-- Favorite Filter -->
            <div class="flex items-center gap-2">
                <a href="{{ route('quotes.index') }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ !$onlyFavorites ? 'bg-stone-900 text-white' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                    All Quotes ({{ $totalQuotes }})
                </a>
                <a href="{{ route('quotes.index', ['favorite' => 1]) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $onlyFavorites ? 'bg-amber-500 text-white shadow-xs' : 'bg-white border border-stone-200 text-stone-700 hover:bg-stone-100' }}">
                    ★ Favorites Only ({{ $favoritesCount }})
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Add New Quote Form (SRS FR-070) -->
            <div class="lg:col-span-4">
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs sticky top-24">
                    <h2 class="font-serif text-lg font-bold text-stone-900 mb-1">Save a New Quote</h2>
                    <p class="text-xs text-stone-500 mb-4">Add a quote from a book or inspiring thinker.</p>

                    <form method="POST" action="{{ route('quotes.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="quote_text" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Quote Text</label>
                            <textarea id="quote_text" name="text" rows="4" required placeholder="“The soul becomes dyed with the color of its thoughts...”"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white font-serif">{{ old('text') }}</textarea>
                        </div>

                        <div>
                            <label for="book_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Book from Library (Optional)</label>
                            <select id="book_id" name="book_id" class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                                <option value="">-- None / Outside Book --</option>
                                @foreach($libraryBooks as $item)
                                    <option value="{{ $item->book->id }}" {{ old('book_id') == $item->book->id ? 'selected' : '' }}>
                                        {{ $item->book->title }} ({{ $item->book->author }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="source" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Author / Speaker / Source (Optional)</label>
                            <input id="source" type="text" name="source" placeholder="e.g. Marcus Aurelius" value="{{ old('source') }}"
                                class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                            Save Quote
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Quotes Cards Grid (SRS FR-071, FR-072, FR-073) -->
            <div class="lg:col-span-8">
                @if($quotes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($quotes as $quote)
                            <div class="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between group">
                                <div>
                                    <div class="flex items-center justify-between text-stone-400 mb-3">
                                        <!-- Copy Quote Action (SRS FR-073) -->
                                        <button type="button" onclick="copyQuote('{{ addslashes($quote->text) }}', this)"
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-stone-500 hover:text-stone-900 transition px-2 py-1 rounded-md hover:bg-stone-100" title="Copy to clipboard">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                            <span>Copy</span>
                                        </button>

                                        <!-- Favorite Toggle (SRS FR-072) -->
                                        <form method="POST" action="{{ route('quotes.toggle-favorite', $quote) }}">
                                            @csrf
                                            <button type="submit" class="p-1 rounded-full hover:bg-stone-100 transition" title="Toggle Favorite">
                                                <svg class="w-4 h-4 {{ $quote->is_favorite ? 'text-amber-500 fill-amber-500' : 'text-stone-300 hover:text-amber-500' }}" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <p class="font-serif italic text-sm text-stone-800 leading-relaxed">
                                        “{{ $quote->text }}”
                                    </p>
                                </div>

                                <div class="mt-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs">
                                    <div class="text-stone-500 font-medium truncate max-w-[180px]">
                                        @if($quote->book)
                                            <a href="{{ route('books.show', $quote->book) }}" class="text-stone-900 font-semibold hover:underline">
                                                {{ $quote->book->title }}
                                            </a>
                                        @else
                                            <span>{{ $quote->source ?? 'Unknown' }}</span>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('quotes.destroy', $quote) }}" onsubmit="return confirm('Remove quote?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-stone-400 hover:text-red-600 transition text-[11px]">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $quotes->links() }}
                    </div>
                @else
                    <div class="p-12 text-center bg-white rounded-3xl border border-stone-200/80 text-stone-500 text-xs">
                        No quotes found in this view. Use the form on the left to capture quotes that move you!
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function copyQuote(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="text-emerald-600 font-bold">✓ Copied!</span>';
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        });
    }
</script>
@endsection
