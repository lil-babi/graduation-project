@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-serif text-3xl font-bold text-stone-900">Book Reviews & Ratings</h1>
            <p class="text-sm text-stone-600 mt-1">Manage your written book reviews and read reflections from the community.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: My Reviews -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-lg font-bold text-stone-900 mb-4 pb-3 border-b border-stone-100">
                        My Reviews ({{ $myReviews->total() }})
                    </h2>

                    @if($myReviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($myReviews as $review)
                                <div class="p-5 rounded-2xl bg-stone-50 border border-stone-200/70">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <div>
                                            <a href="{{ route('books.show', $review->book) }}" class="font-serif font-bold text-sm text-stone-900 hover:text-brand-700">
                                                {{ $review->book->title }}
                                            </a>
                                            <p class="text-[11px] text-stone-500">{{ $review->book->author }} • {{ $review->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <div class="flex items-center gap-1 text-amber-500 font-bold text-xs bg-white px-2 py-1 rounded-lg border border-stone-200">
                                            <span>{{ $review->rating }} ★</span>
                                        </div>
                                    </div>

                                    <p class="text-xs text-stone-700 leading-relaxed font-serif whitespace-pre-line mt-2">
                                        {{ $review->body }}
                                    </p>

                                    <div class="mt-4 pt-3 border-t border-stone-200/60 flex items-center justify-between text-xs">
                                        <a href="{{ route('books.show', $review->book) }}" class="text-brand-700 font-semibold hover:underline">
                                            View Book Page
                                        </a>

                                        <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Delete Review
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $myReviews->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-xs text-stone-500">
                            You haven't written any book reviews yet. Visit any book in the catalog to leave your star rating and thoughts!
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Community Reviews -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs">
                    <h2 class="font-serif text-lg font-bold text-stone-900 mb-4 pb-3 border-b border-stone-100">
                        Recent Community Reviews
                    </h2>

                    <div class="space-y-4">
                        @forelse($communityReviews as $cRev)
                            <div class="p-4 rounded-xl bg-stone-50/60 border border-stone-200/60 text-xs">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-semibold text-stone-900">{{ $cRev->user->name }}</span>
                                    <span class="text-amber-500 font-bold">{{ $cRev->rating }} ★</span>
                                </div>
                                <div class="text-[11px] text-brand-700 font-medium mb-1">
                                    On <a href="{{ route('books.show', $cRev->book) }}" class="underline">{{ $cRev->book->title }}</a>
                                </div>
                                <p class="text-stone-600 font-serif line-clamp-3 italic">“{{ $cRev->body }}”</p>
                            </div>
                        @empty
                            <p class="text-center py-6 text-xs text-stone-500">No other reviews recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
