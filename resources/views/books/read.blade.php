@extends('layouts.app')

@section('content')
<div class="bg-stone-900 text-stone-100 min-h-screen flex flex-col">
    <!-- Reader Top Bar -->
    <div class="bg-stone-950 border-b border-stone-800 px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('books.show', $book) }}" class="text-xs font-semibold text-stone-400 hover:text-white flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Back to Book Details</span>
            </a>
            <div class="h-4 w-px bg-stone-800 hidden sm:block"></div>
            <div>
                <h1 class="font-serif font-bold text-sm text-white truncate max-w-xs sm:max-w-md">{{ $book->title }}</h1>
                <p class="text-[11px] text-stone-400 truncate">{{ $book->author }} • {{ $book->page_count }} Pages</p>
            </div>
        </div>

        <!-- Quick Log Pages Read Form (SRS FR-041) -->
        @auth
            <div class="flex items-center gap-3">
                @if($userBook)
                    <span class="text-xs text-stone-400 hidden md:inline">
                        Currently on page <strong class="text-white">{{ $userBook->current_page }}</strong> of {{ $book->page_count }}
                    </span>
                @endif

                <form method="POST" action="{{ route('progress.store') }}" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <input type="hidden" name="reading_date" value="{{ date('Y-m-d') }}">
                    <input type="number" name="pages_read" min="1" max="500" placeholder="Pages" required
                        class="w-20 px-2.5 py-1.5 rounded-lg bg-stone-800 border border-stone-700 text-xs text-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-brand-700 hover:bg-brand-600 text-xs font-semibold text-white transition">
                        Log Reading
                    </button>
                </form>
            </div>
        @else
            <div class="text-xs text-stone-400">
                <a href="{{ route('login') }}" class="text-white underline">Log in</a> to track your reading progress.
            </div>
        @endauth
    </div>

    <!-- Viewer Area (SRS FR-090 & FR-091) -->
    <div class="flex-1 w-full bg-stone-900 flex flex-col items-center justify-center p-2 sm:p-4">
        @if($pdfExists)
            <div class="w-full max-w-5xl h-[82vh] bg-stone-950 rounded-xl overflow-hidden border border-stone-800 shadow-2xl relative">
                <iframe src="{{ $pdfUrl }}#toolbar=1&navpanes=1" class="w-full h-full border-0 rounded-xl" title="{{ $book->title }} PDF Viewer">
                    <p class="p-6 text-center text-sm text-stone-400">
                        Your browser does not support inline PDF viewing.
                        <a href="{{ $pdfUrl }}" target="_blank" class="text-brand-400 underline font-semibold ml-1">Open PDF in new tab</a>.
                    </p>
                </iframe>
            </div>
        @else
            <!-- Graceful Missing File Handling (SRS FR-091 & AT-017) -->
            <div class="text-center p-8 max-w-md bg-stone-950 rounded-2xl border border-stone-800">
                <div class="w-12 h-12 rounded-xl bg-stone-800 text-amber-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="font-serif font-bold text-base text-white">Digital Document Unavailable</h3>
                <p class="text-xs text-stone-400 mt-1 leading-relaxed">
                    The requested file is either being processed or has not been uploaded to storage yet. The rest of the book details and reading features remain fully functional.
                </p>
                <div class="mt-4">
                    <a href="{{ route('books.show', $book) }}" class="inline-flex px-4 py-2 rounded-xl text-xs font-semibold bg-stone-800 hover:bg-stone-700 text-white transition">
                        Return to Book Overview
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
