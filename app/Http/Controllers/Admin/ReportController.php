<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        // 1. User breakdown
        $totalUsers = User::where('role', '!=', 'admin')->count();

        $activePremiumSubscribers = Subscription::where('plan_code', 'premium')
            ->where('status', 'active')
            ->where(function ($q): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->distinct('user_id')
            ->count('user_id');

        $freeUsers = max(0, $totalUsers - $activePremiumSubscribers);

        $expiredSubscriptionsCount = Subscription::where('plan_code', 'premium')
            ->where(function ($q): void {
                $q->where('status', 'expired')
                    ->orWhere('status', 'canceled')
                    ->orWhere('ends_at', '<=', now());
            })
            ->count();

        // 2. Simulated Transactions (Demo)
        $simulatedTransactions = Transaction::where('payment_mode', 'simulated')
            ->with(['user', 'subscription'])
            ->latest()
            ->paginate(15);

        $simulatedCount = Transaction::where('payment_mode', 'simulated')->count();
        $simulatedSum = Transaction::where('payment_mode', 'simulated')->where('status', 'completed')->sum('amount');

        // 3. Confirmed Real Revenue (Strictly 0.00 ETB for demo per SRS FR-113)
        $realCount = Transaction::where('payment_mode', 'real')->count();
        $realRevenueSum = Transaction::where('payment_mode', 'real')->where('status', 'completed')->sum('amount');

        return view('admin.reports.index', compact(
            'totalUsers',
            'freeUsers',
            'activePremiumSubscribers',
            'expiredSubscriptionsCount',
            'simulatedTransactions',
            'simulatedCount',
            'simulatedSum',
            'realCount',
            'realRevenueSum'
        ));
    }
}
