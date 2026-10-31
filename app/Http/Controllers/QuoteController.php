<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $onlyFavorites = $request->boolean('favorite');

        $query = $user->quotes()->with('book')->latest();

        if ($onlyFavorites) {
            $query->where('is_favorite', true);
        }

        $quotes = $query->paginate(12)->withQueryString();

        $libraryBooks = $user->userBooks()->with('book')->get();

        $favoritesCount = $user->quotes()->where('is_favorite', true)->count();
        $totalQuotes = $user->quotes()->count();

        return view('quotes.index', compact('quotes', 'libraryBooks', 'onlyFavorites', 'favoritesCount', 'totalQuotes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'min:3', 'max:1500'],
            'book_id' => ['nullable', 'exists:books,id'],
            'source' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        $user->quotes()->create([
            'text' => strip_tags($validated['text']),
            'book_id' => $validated['book_id'] ?? null,
            'source' => $validated['source'] ?? null,
            'is_favorite' => false,
        ]);

        return back()->with('success', 'Quote saved to your personal collection.');
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action: You cannot modify another reader\'s quote.');
        }

        $validated = $request->validate([
            'text' => ['required', 'string', 'min:3', 'max:1500'],
            'book_id' => ['nullable', 'exists:books,id'],
            'source' => ['nullable', 'string', 'max:255'],
        ]);

        $quote->update([
            'text' => strip_tags($validated['text']),
            'book_id' => $validated['book_id'] ?? null,
            'source' => $validated['source'] ?? null,
        ]);

        return back()->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        if ($quote->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action: You cannot delete another reader\'s quote.');
        }

        $quote->delete();

        return back()->with('success', 'Quote removed from your collection.');
    }

    public function toggleFavorite(Quote $quote): RedirectResponse
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $quote->update([
            'is_favorite' => ! $quote->is_favorite,
        ]);

        $status = $quote->is_favorite ? 'marked as favorite' : 'removed from favorites';

        return back()->with('success', "Quote {$status}.");
    }
}
