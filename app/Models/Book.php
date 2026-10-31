<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'description',
        'cover_path',
        'page_count',
        'publication_year',
        'pdf_path',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'page_count' => 'integer',
            'publication_year' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<UserBook, $this>
     */
    public function userBooks(): HasMany
    {
        return $this->hasMany(UserBook::class);
    }

    /**
     * @return HasMany<ReadingLog, $this>
     */
    public function readingLogs(): HasMany
    {
        return $this->hasMany(ReadingLog::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * @return HasMany<Quote, $this>
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * @return HasMany<ReadingPlan, $this>
     */
    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }

    public function averageRating(): float
    {
        $avg = $this->reviews()->avg('rating');

        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    public function userStatus(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        return $this->userBooks()->where('user_id', $user->id)->value('status');
    }

    public function userCurrentPage(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return (int) $this->userBooks()->where('user_id', $user->id)->value('current_page') ?: 0;
    }

    public function userProgressPercent(?User $user): int
    {
        if (! $user || $this->page_count <= 0) {
            return 0;
        }

        $currentPage = $this->userCurrentPage($user);
        $percent = ($currentPage / $this->page_count) * 100;

        return (int) min(100, max(0, round($percent)));
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeCategory(Builder $query, ?string $slug): Builder
    {
        if (blank($slug) || $slug === 'all') {
            return $query;
        }

        return $query->whereHas('category', function (Builder $q) use ($slug): void {
            $q->where('slug', $slug);
        });
    }
}
