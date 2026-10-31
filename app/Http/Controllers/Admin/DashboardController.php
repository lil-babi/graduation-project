<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $freeUsers = User::where('role', '!=', 'admin')
            ->whereDoesntHave('subscriptions', function ($q): void {
                $q->where('plan_code', 'premium')->where('status', 'active');
            })
            ->count();

        $activePremiumUsers = User::whereHas('subscriptions', function ($q): void {
            $q->where('plan_code', 'premium')
                ->where('status', 'active')
                ->where(function ($sq): void {
                    $sq->whereNull('ends_at')->orWhere('ends_at', '>', now());
                });
        })->count();

        $totalBooks = Book::count();
        $totalCategories = Category::count();

        // SRS FR-112 & FR-113: Strict separation between simulated transactions and actual revenue
        $simulatedTransactionsCount = Transaction::where('payment_mode', 'simulated')->count();
        $simulatedRevenueTotal = Transaction::where('payment_mode', 'simulated')->where('status', 'completed')->sum('amount');

        $realRevenueTotal = Transaction::where('payment_mode', 'real')->where('status', 'completed')->sum('amount'); // Always 0.00 in demo

        $recentTransactions = Transaction::with(['user', 'subscription'])
            ->latest()
            ->take(6)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'freeUsers',
            'activePremiumUsers',
            'totalBooks',
            'totalCategories',
            'simulatedTransactionsCount',
            'simulatedRevenueTotal',
            'realRevenueTotal',
            'recentTransactions',
            'recentUsers'
        ));
    }
}
