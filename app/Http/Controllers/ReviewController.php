<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $myReviews = $user->reviews()
            ->with('book.category')
            ->latest()
            ->paginate(10);

        $communityReviews = Review::where('user_id', '!=', $user->id)
            ->with(['book', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('reviews.index', compact('myReviews', 'communityReviews'));
    }

    public function store(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $user = Auth::user();

        // One review per user per book
        Review::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            [
                'rating' => $validated['rating'],
                'body' => strip_tags($validated['body']), // prevent script injection FR-065
            ]
        );

        return back()->with('success', 'Your review has been submitted.');
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        // SRS FR-063 & AT-009: Users shall not be able to modify another user's review unless authorized as administrator
        if ($review->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action: You cannot edit another reader\'s review.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'body' => strip_tags($validated['body']),
        ]);

        return back()->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        // SRS FR-063 & AT-009: Only owner or admin can delete
        if ($review->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action: You cannot delete another reader\'s review.');
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
