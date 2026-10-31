<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\UserBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $user = Auth::user();

        $query = $user->userBooks()
            ->with(['book.category'])
            ->latest('added_at');

        if ($status !== 'all' && in_array($status, ['want_to_read', 'reading', 'completed'], true)) {
            $query->where('status', $status);
        }

        $userBooks = $query->paginate(12)->withQueryString();

        $counts = [
            'all' => $user->userBooks()->count(),
            'reading' => $user->userBooks()->where('status', 'reading')->count(),
            'want_to_read' => $user->userBooks()->where('status', 'want_to_read')->count(),
            'completed' => $user->userBooks()->where('status', 'completed')->count(),
        ];

        return view('library.index', compact('userBooks', 'status', 'counts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'status' => ['nullable', 'in:want_to_read,reading,completed'],
        ]);

        $user = Auth::user();

        // SRS FR-025 & AT-005: Prevent duplicate entries of the same catalog book
        $existing = $user->userBooks()->where('book_id', $validated['book_id'])->first();

        if ($existing) {
            return back()->with('info', 'This book is already in your personal library.');
        }

        $book = Book::findOrFail($validated['book_id']);
        $status = $validated['status'] ?? 'want_to_read';

        $user->userBooks()->create([
            'book_id' => $book->id,
            'status' => $status,
            'current_page' => $status === 'completed' ? $book->page_count : 0,
            'added_at' => now(),
            'completed_at' => $status === 'completed' ? now() : null,
        ]);

        return back()->with('success', "'{$book->title}' was added to your library.");
    }

    public function update(Request $request, UserBook $userBook): RedirectResponse
    {
        // SRS FR-036: A user shall not be able to view or modify another user's private library records
        if ($userBook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:want_to_read,reading,completed'],
            'current_page' => ['nullable', 'integer', 'min:0'],
        ]);

        $book = $userBook->book;
        $status = $validated['status'];
        $currentPage = isset($validated['current_page']) ? (int) $validated['current_page'] : $userBook->current_page;

        if ($status === 'completed') {
            $currentPage = $book->page_count;
            $completedAt = $userBook->completed_at ?? now();
        } else {
            $completedAt = null;
        }

        // Cap currentPage at book page_count
        if ($book->page_count > 0 && $currentPage > $book->page_count) {
            $currentPage = $book->page_count;
        }

        $userBook->update([
            'status' => $status,
            'current_page' => $currentPage,
            'completed_at' => $completedAt,
        ]);

        return back()->with('success', 'Book status updated successfully.');
    }

    public function destroy(UserBook $userBook): RedirectResponse
    {
        if ($userBook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $title = $userBook->book->title;
        $userBook->delete();

        return back()->with('success', "'{$title}' was removed from your library.");
    }
}
