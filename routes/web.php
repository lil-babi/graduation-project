<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StreakController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/discover', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');

/*
|--------------------------------------------------------------------------
| Guest Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PDF Reader
    Route::get('/books/{book}/read', [BookController::class, 'read'])->name('books.read');

    // Personal Library
    Route::get('/my-books', [LibraryController::class, 'index'])->name('library.index');
    Route::post('/my-books', [LibraryController::class, 'store'])->name('library.store');
    Route::patch('/my-books/{userBook}', [LibraryController::class, 'update'])->name('library.update');
    Route::delete('/my-books/{userBook}', [LibraryController::class, 'destroy'])->name('library.destroy');

    // Reading Progress & Goals
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::post('/progress/log', [ProgressController::class, 'store'])->name('progress.store');

    // Streaks
    Route::get('/streak', [StreakController::class, 'index'])->name('streak.index');

    // Reviews & Ratings
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Quotes
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::post('/quotes/{quote}/toggle-favorite', [QuoteController::class, 'toggleFavorite'])->name('quotes.toggle-favorite');

    // Recommendations
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Monetization & Simulated Checkout
    Route::get('/checkout/simulated', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/checkout/simulated', [SubscriptionController::class, 'processSimulated'])->name('subscription.process-simulated');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/simulate-expire', [SubscriptionController::class, 'simulateExpire'])->name('subscription.simulate-expire');
});

/*
|--------------------------------------------------------------------------
| Premium-Only Features (Middleware: auth, premium)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'premium'])->prefix('premium')->name('premium.')->group(function (): void {
    Route::get('/analytics', [PremiumController::class, 'analytics'])->name('analytics');
    Route::get('/plans', [PremiumController::class, 'readingPlans'])->name('plans');
    Route::post('/plans', [PremiumController::class, 'storeReadingPlan'])->name('plans.store');
    Route::patch('/plans/{plan}', [PremiumController::class, 'updateReadingPlan'])->name('plans.update');
    Route::delete('/plans/{plan}', [PremiumController::class, 'destroyReadingPlan'])->name('plans.destroy');
});

/*
|--------------------------------------------------------------------------
| Administrator Management (Middleware: auth, admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('books', AdminBookController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'destroy']);
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});
