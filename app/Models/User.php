<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'daily_goal_pages',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'daily_goal_pages' => 'integer',
            'preferences' => 'array',
        ];
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
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Quote, $this>
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    /**
     * @return HasMany<ReadingPlan, $this>
     */
    public function readingPlans(): HasMany
    {
        return $this->hasMany(ReadingPlan::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->first();
    }

    public function isPremium(): bool
    {
        $sub = $this->activeSubscription();

        return $sub !== null && $sub->plan_code === 'premium';
    }

    public function todayPagesRead(): int
    {
        $today = Carbon::today(config('app.timezone', 'UTC'))->toDateString();

        return (int) $this->readingLogs()
            ->whereDate('reading_date', $today)
            ->sum('pages_read');
    }

    public function totalPagesRead(): int
    {
        return (int) $this->readingLogs()->sum('pages_read');
    }

    public function booksReadCount(): int
    {
        return (int) $this->userBooks()->where('status', 'completed')->count();
    }

    /**
     * Calculate current consecutive reading days.
     * SRS FR-050 to FR-055:
     * - Reading > 0 pages qualifies as a reading day.
     * - Daily page goal does not have to be met.
     * - 0 pages is not a reading day.
     * - Missing calendar day breaks streak.
     * - Handled consistently using app timezone.
     */
    public function calculateCurrentStreak(): int
    {
        $timezone = config('app.timezone', 'UTC');
        $today = Carbon::today($timezone);

        // Group pages read by date, only dates with sum(pages_read) > 0
        $readingDays = $this->readingLogs()
            ->selectRaw('reading_date, SUM(pages_read) as total_pages')
            ->groupBy('reading_date')
            ->having('total_pages', '>', 0)
            ->orderByDesc('reading_date')
            ->pluck('reading_date')
            ->map(fn ($date) => Carbon::parse($date, $timezone)->startOfDay())
            ->values();

        if ($readingDays->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $checkDate = $today->copy();

        // If today has reading, streak starts from today.
        // If today has no reading yet, check if yesterday had reading. If yes, streak continues from yesterday.
        $hasToday = $readingDays->contains(fn ($d) => $d->equalTo($checkDate));

        if (! $hasToday) {
            $checkDate->subDay();
            $hasYesterday = $readingDays->contains(fn ($d) => $d->equalTo($checkDate));
            if (! $hasYesterday) {
                return 0;
            }
        }

        while ($readingDays->contains(fn ($d) => $d->equalTo($checkDate))) {
            $streak++;
            $checkDate->subDay();
        }

        return $streak;
    }

    public function calculateLongestStreak(): int
    {
        $timezone = config('app.timezone', 'UTC');

        $readingDays = $this->readingLogs()
            ->selectRaw('reading_date, SUM(pages_read) as total_pages')
            ->groupBy('reading_date')
            ->having('total_pages', '>', 0)
            ->orderBy('reading_date')
            ->pluck('reading_date')
            ->map(fn ($date) => Carbon::parse($date, $timezone)->startOfDay())
            ->values();

        if ($readingDays->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $currentRun = 1;

        for ($i = 1; $i < $readingDays->count(); $i++) {
            $diff = $readingDays[$i - 1]->diffInDays($readingDays[$i]);
            if ($diff === 1) {
                $currentRun++;
                if ($currentRun > $longest) {
                    $longest = $currentRun;
                }
            } else {
                $currentRun = 1;
            }
        }

        return $longest;
    }
}
