<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;

class RecommendationService
{
    /**
     * Get tailored recommendations with explicit human-readable reasons.
     *
     * @return array<int, array{book: Book, reason: string, badge: string}>
     */
    public function getRecommendations(?User $user, int $limit = 6): array
    {
        $recommendations = [];
        $recommendedBookIds = [];

        if ($user) {
            $userBookIds = $user->userBooks()->pluck('book_id')->toArray();

            // 1. Based on user reading preferences (if configured in user preferences)
            $favCategory = $user->preferences['favorite_category'] ?? null;
            if ($favCategory) {
                $prefBooks = Book::published()
                    ->whereNotIn('id', array_merge($userBookIds, $recommendedBookIds))
                    ->whereHas('category', fn ($q) => $q->where('slug', $favCategory)->orWhere('name', $favCategory))
                    ->take(2)
                    ->get();

                foreach ($prefBooks as $book) {
                    $recommendations[] = [
                        'book' => $book,
                        'reason' => "Curated for your selected interest in {$book->category->name}",
                        'badge' => 'Preferred Genre',
                    ];
                    $recommendedBookIds[] = $book->id;
                }
            }

            // 2. Based on categories of books already in user's library
            $readCategoryIds = Book::whereIn('id', $userBookIds)
                ->pluck('category_id')
                ->unique()
                ->toArray();

            if (! empty($readCategoryIds)) {
                $categoryBooks = Book::published()
                    ->whereNotIn('id', array_merge($userBookIds, $recommendedBookIds))
                    ->whereIn('category_id', $readCategoryIds)
                    ->take(3)
                    ->get();

                foreach ($categoryBooks as $book) {
                    $recommendations[] = [
                        'book' => $book,
                        'reason' => "Because you saved books in {$book->category->name}",
                        'badge' => 'From Your Reading History',
                    ];
                    $recommendedBookIds[] = $book->id;
                }
            }
        }

        // 3. Fallback / Popular Books to fill remaining slots
        $remainingCount = $limit - count($recommendations);
        if ($remainingCount > 0) {
            $excludeIds = $user ? array_merge($user->userBooks()->pluck('book_id')->toArray(), $recommendedBookIds) : $recommendedBookIds;

            $popularBooks = Book::published()
                ->whereNotIn('id', $excludeIds)
                ->withCount('reviews')
                ->orderByDesc('reviews_count')
                ->take($remainingCount)
                ->get();

            foreach ($popularBooks as $book) {
                $recommendations[] = [
                    'book' => $book,
                    'reason' => "Highly rated by the BookPlatform community in {$book->category->name}",
                    'badge' => 'Community Favorite',
                ];
                $recommendedBookIds[] = $book->id;
            }
        }

        // 4. In case library is full or few books, ensure some books are returned
        if (empty($recommendations)) {
            $books = Book::published()->take($limit)->get();
            foreach ($books as $book) {
                $recommendations[] = [
                    'book' => $book,
                    'reason' => "Recommended reading in {$book->category->name}",
                    'badge' => 'Editor Pick',
                ];
            }
        }

        return array_slice($recommendations, 0, $limit);
    }
}
