<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected StreakService $streakService,
        protected RecommendationService $recommendationService
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::today($timezone)->toDateString();

        $todayPages = $user->todayPagesRead();
        $dailyGoal = $user->daily_goal_pages ?? 20;
        $goalPercent = $dailyGoal > 0 ? (int) min(100, round(($todayPages / $dailyGoal) * 100)) : 0;

        $currentStreak = $this->streakService->getCurrentStreak($user);
        $longestStreak = $this->streakService->getLongestStreak($user);

        // Books currently being read
        $currentlyReading = $user->userBooks()
            ->where('status', 'reading')
            ->with('book.category')
            ->latest('updated_at')
            ->take(3)
            ->get();

        // Want to read books
        $wantToRead = $user->userBooks()
            ->where('status', 'want_to_read')
            ->with('book.category')
            ->latest('added_at')
            ->take(3)
            ->get();

        // Recent reading logs
        $recentLogs = $user->readingLogs()
            ->with('book')
            ->orderByDesc('reading_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Recommendations with reasons
        $recommendations = $this->recommendationService->getRecommendations($user, 4);

        $subscription = $user->activeSubscription();
        $isPremium = $user->isPremium();
        $upcomingFeatures = config('plans.upcoming_features', []);

        return view('dashboard', compact(
            'user',
            'todayPages',
            'dailyGoal',
            'goalPercent',
            'currentStreak',
            'longestStreak',
            'currentlyReading',
            'wantToRead',
            'recentLogs',
            'recommendations',
            'subscription',
            'isPremium',
            'upcomingFeatures'
        ));
    }
}
