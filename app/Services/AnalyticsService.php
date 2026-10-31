<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Compile rich reading analytics for Premium users.
     *
     * @return array<string, mixed>
     */
    public function getPremiumAnalytics(User $user): array
    {
        $timezone = config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);
        $thirtyDaysAgo = $now->copy()->subDays(30);

        // 1. Velocity (Last 30 Days)
        $pagesLast30Days = (int) $user->readingLogs()
            ->whereDate('reading_date', '>=', $thirtyDaysAgo->toDateString())
            ->sum('pages_read');

        $activeDaysLast30 = (int) $user->readingLogs()
            ->whereDate('reading_date', '>=', $thirtyDaysAgo->toDateString())
            ->where('pages_read', '>', 0)
            ->distinct('reading_date')
            ->count('reading_date');

        $velocityPerPagePerDay = round($pagesLast30Days / 30, 1);
        $velocityPerActiveDay = $activeDaysLast30 > 0 ? round($pagesLast30Days / $activeDaysLast30, 1) : 0;

        // 2. Monthly Trend (Last 6 Months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth()->toDateString();
            $monthEnd = $month->copy()->endOfMonth()->toDateString();

            $monthPages = (int) $user->readingLogs()
                ->whereBetween('reading_date', [$monthStart, $monthEnd])
                ->sum('pages_read');

            $monthlyTrends[] = [
                'label' => $month->format('M Y'),
                'pages' => $monthPages,
            ];
        }

        // 3. Category Breakdown of Saved Books
        $categoryBreakdown = $user->userBooks()
            ->join('books', 'user_books.book_id', '=', 'books.id')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, COUNT(*) as book_count')
            ->groupBy('categories.name')
            ->pluck('book_count', 'category_name')
            ->toArray();

        // 4. Forecasts for Currently Reading Books
        $currentlyReading = $user->userBooks()
            ->where('status', 'reading')
            ->with('book')
            ->get()
            ->map(function ($item) use ($velocityPerPagePerDay) {
                $book = $item->book;
                $remainingPages = max(0, $book->page_count - $item->current_page);
                $estimatedDays = $velocityPerPagePerDay > 0
                    ? ceil($remainingPages / $velocityPerPagePerDay)
                    : ceil($remainingPages / 20); // fallback to default daily goal 20

                $estimatedFinishDate = Carbon::today()->addDays((int) $estimatedDays)->format('M d, Y');

                return [
                    'book' => $book,
                    'current_page' => $item->current_page,
                    'total_pages' => $book->page_count,
                    'remaining_pages' => $remainingPages,
                    'progress_percent' => $book->page_count > 0 ? round(($item->current_page / $book->page_count) * 100) : 0,
                    'estimated_days_left' => $estimatedDays,
                    'estimated_finish_date' => $estimatedFinishDate,
                ];
            });

        // 5. Total Estimated Reading Hours (avg 1.5 min per page)
        $totalEstimatedHours = round(($user->totalPagesRead() * 1.5) / 60, 1);

        return [
            'total_pages_read' => $user->totalPagesRead(),
            'pages_last_30_days' => $pagesLast30Days,
            'active_days_last_30' => $activeDaysLast30,
            'velocity_daily_avg' => $velocityPerPagePerDay,
            'velocity_active_day_avg' => $velocityPerActiveDay,
            'total_estimated_hours' => $totalEstimatedHours,
            'monthly_trends' => $monthlyTrends,
            'category_breakdown' => $categoryBreakdown,
            'currently_reading_forecasts' => $currentlyReading,
            'current_streak' => $user->calculateCurrentStreak(),
            'longest_streak' => $user->calculateLongestStreak(),
        ];
    }
}
