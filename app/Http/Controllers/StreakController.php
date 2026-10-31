<?php

namespace App\Http\Controllers;

use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StreakController extends Controller
{
    public function __construct(protected StreakService $streakService) {}

    public function index(): View
    {
        $user = Auth::user();
        $currentStreak = $this->streakService->getCurrentStreak($user);
        $longestStreak = $this->streakService->getLongestStreak($user);
        $activityMap = $this->streakService->getRecentActivityMap($user, 30);
        $todayRead = $user->todayPagesRead() > 0;
        $appTimezone = config('app.timezone', 'UTC');

        return view('streak.index', compact(
            'currentStreak',
            'longestStreak',
            'activityMap',
            'todayRead',
            'appTimezone'
        ));
    }
}
