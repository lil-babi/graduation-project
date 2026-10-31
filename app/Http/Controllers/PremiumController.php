<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PremiumController extends Controller
{
    public function __construct(protected AnalyticsService $analyticsService) {}

    public function analytics(): View
    {
        $user = Auth::user();
        $analytics = $this->analyticsService->getPremiumAnalytics($user);
        $upcomingFeatures = config('plans.upcoming_features', []);

        return view('premium.analytics', compact('user', 'analytics', 'upcomingFeatures'));
    }

    public function readingPlans(): View
    {
        $user = Auth::user();

        $readingPlans = $user->readingPlans()
            ->with('book.category')
            ->latest()
            ->get();

        $availableBooks = $user->userBooks()
            ->whereIn('status', ['reading', 'want_to_read'])
            ->with('book')
            ->get();

        $upcomingFeatures = config('plans.upcoming_features', []);

        return view('premium.plans', compact('user', 'readingPlans', 'availableBooks', 'upcomingFeatures'));
    }

    public function storeReadingPlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'target_completion_date' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($validated['book_id']);

        // Calculate pages left
        $userBook = $user->userBooks()->where('book_id', $book->id)->first();
        $currentPage = $userBook ? $userBook->current_page : 0;
        $remainingPages = max(1, $book->page_count - $currentPage);

        $targetDate = Carbon::parse($validated['target_completion_date']);
        $daysAvailable = max(1, Carbon::today()->diffInDays($targetDate));

        $dailyTarget = (int) ceil($remainingPages / $daysAvailable);

        ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_completion_date' => $validated['target_completion_date'],
            'daily_target_pages' => $dailyTarget,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
        ]);

        return back()->with('success', "Personalized reading plan for '{$book->title}' generated! Target pace: {$dailyTarget} pages/day.");
    }

    public function updateReadingPlan(Request $request, ReadingPlan $plan): RedirectResponse
    {
        if ($plan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,completed,abandoned'],
        ]);

        $plan->update(['status' => $validated['status']]);

        return back()->with('success', 'Reading plan status updated.');
    }

    public function destroyReadingPlan(ReadingPlan $plan): RedirectResponse
    {
        if ($plan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $plan->delete();

        return back()->with('success', 'Reading plan removed.');
    }
}
