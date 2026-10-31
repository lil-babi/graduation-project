<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class StreakService
{
    /**
     * Calculate current streak following SRS rules (FR-050 to FR-055).
     */
    public function getCurrentStreak(User $user): int
    {
        return $user->calculateCurrentStreak();
    }

    /**
     * Calculate longest streak ever achieved by the user.
     */
    public function getLongestStreak(User $user): int
    {
        return $user->calculateLongestStreak();
    }

    /**
     * Get recent 30-day activity matrix for streak visualization.
     *
     * @return array<int, array{date: string, day_name: string, pages: int, is_active: bool, is_today: bool}>
     */
    public function getRecentActivityMap(User $user, int $days = 30): array
    {
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::today($timezone);
        $startDate = $today->copy()->subDays($days - 1);

        $logs = $user->readingLogs()
            ->whereDate('reading_date', '>=', $startDate->toDateString())
            ->selectRaw('reading_date, SUM(pages_read) as total_pages')
            ->groupBy('reading_date')
            ->pluck('total_pages', 'reading_date')
            ->toArray();

        $activity = [];
        $cursor = $startDate->copy();

        while ($cursor->lessThanOrEqualTo($today)) {
            $dateStr = $cursor->toDateString();
            $pages = isset($logs[$dateStr]) ? (int) $logs[$dateStr] : 0;

            $activity[] = [
                'date' => $dateStr,
                'day_name' => $cursor->format('D'),
                'day_number' => $cursor->format('j'),
                'pages' => $pages,
                'is_active' => $pages > 0,
                'is_today' => $cursor->equalTo($today),
            ];

            $cursor->addDay();
        }

        return $activity;
    }
}
