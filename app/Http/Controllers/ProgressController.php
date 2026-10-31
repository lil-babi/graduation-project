<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingLog;
use App\Models\UserBook;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::today($timezone)->toDateString();

        $dailyGoal = $user->daily_goal_pages ?? 20;
        $todayPages = $user->todayPagesRead();
        $goalPercent = $dailyGoal > 0 ? (int) min(100, round(($todayPages / $dailyGoal) * 100)) : 0;

        $readingLogs = $user->readingLogs()
            ->with('book')
            ->orderByDesc('reading_date')
            ->orderByDesc('id')
            ->paginate(15);

        // Books currently in user's library for easy logging dropdown
        $libraryBooks = $user->userBooks()
            ->whereIn('status', ['reading', 'want_to_read'])
            ->with('book')
            ->get();

        $totalDaysRead = $user->readingLogs()
            ->where('pages_read', '>', 0)
            ->distinct('reading_date')
            ->count('reading_date');

        return view('progress.index', compact(
            'dailyGoal',
            'todayPages',
            'goalPercent',
            'readingLogs',
            'libraryBooks',
            'totalDaysRead',
            'today'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pages_read' => ['required', 'integer', 'min:1'], // AT-007: Rejected or requires positive non-zero pages
            'reading_date' => ['required', 'date', 'before_or_equal:today'],
            'book_id' => ['nullable', 'exists:books,id'],
            'minutes_read' => ['nullable', 'integer', 'min:1'],
        ], [
            'pages_read.min' => 'Recorded pages must be at least 1 page to count as reading activity.',
            'reading_date.before_or_equal' => 'Reading date cannot be in the future.',
        ]);

        $user = Auth::user();

        $log = ReadingLog::create([
            'user_id' => $user->id,
            'book_id' => $validated['book_id'] ?? null,
            'reading_date' => $validated['reading_date'],
            'pages_read' => (int) $validated['pages_read'],
            'minutes_read' => $validated['minutes_read'] ?? null,
        ]);

        // If associated with a book in user's library, update current_page
        if (! empty($validated['book_id'])) {
            $userBook = UserBook::where('user_id', $user->id)
                ->where('book_id', $validated['book_id'])
                ->first();

            $book = Book::find($validated['book_id']);

            if ($userBook && $book) {
                $newCurrent = $userBook->current_page + (int) $validated['pages_read'];
                if ($book->page_count > 0 && $newCurrent >= $book->page_count) {
                    $newCurrent = $book->page_count;
                    $userBook->update([
                        'status' => 'completed',
                        'current_page' => $newCurrent,
                        'completed_at' => now(),
                    ]);
                } else {
                    $userBook->update([
                        'status' => 'reading',
                        'current_page' => $newCurrent,
                    ]);
                }
            }
        }

        return back()->with('success', "Logged {$validated['pages_read']} pages successfully!");
    }
}
