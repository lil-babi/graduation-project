<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function pricing(): View
    {
        $user = Auth::user();
        $plans = config('plans.plans');
        $currency = config('plans.currency', 'ETB');
        $upcomingFeatures = config('plans.upcoming_features', []);

        $activeSubscription = $user ? $user->activeSubscription() : null;
        $isPremium = $user ? $user->isPremium() : false;

        return view('pricing', compact('plans', 'currency', 'activeSubscription', 'isPremium', 'upcomingFeatures'));
    }

    public function checkout(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isPremium()) {
            return redirect()->route('dashboard')
                ->with('info', 'You already have an active Premium subscription.');
        }

        $plan = config('plans.plans.premium');
        $currency = config('plans.currency', 'ETB');

        return view('checkout.simulated', compact('plan', 'currency', 'user'));
    }

    public function processSimulated(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'confirm_demo' => ['required', 'accepted'],
        ], [
            'confirm_demo.accepted' => 'Please confirm that you understand this is a simulated checkout demonstration.',
        ]);

        $premiumConfig = config('plans.plans.premium');
        $amount = $premiumConfig['price'] ?? 99.00;
        $currency = config('plans.currency', 'ETB');

        // Check if there is already an active subscription
        $existing = $user->activeSubscription();
        if ($existing && $existing->plan_code === 'premium') {
            return redirect()->route('dashboard')
                ->with('info', 'Your Premium membership is already active.');
        }

        // 1. Create Simulated Transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'completed',
            'payment_mode' => 'simulated', // Explicitly marked as simulated SRS FR-111 & FR-112
            'provider_reference' => 'SIM-'.strtoupper(Str::random(10)),
            'paid_at' => now(),
        ]);

        // 2. Grant or Update Premium Subscription for 30 Days
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_code' => 'premium',
            'status' => 'active',
            'price_amount' => $amount,
            'currency' => $currency,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'payment_mode' => 'simulated',
        ]);

        $transaction->update(['subscription_id' => $subscription->id]);

        return redirect()->route('premium.analytics')
            ->with('success', '🎉 Demonstration upgrade complete! You now have active Premium access for 30 days.');
    }

    public function cancel(): RedirectResponse
    {
        $user = Auth::user();
        $sub = $user->activeSubscription();

        if ($sub && $sub->plan_code === 'premium') {
            $sub->update([
                'status' => 'canceled',
            ]);

            return redirect()->route('pricing')
                ->with('info', 'Your Premium subscription has been canceled. You have returned to the Free plan.');
        }

        return back()->with('info', 'No active Premium subscription found to cancel.');
    }

    public function simulateExpire(): RedirectResponse
    {
        $user = Auth::user();
        $sub = $user->activeSubscription();

        if ($sub) {
            $sub->update([
                'status' => 'expired',
                'ends_at' => now()->subMinute(),
            ]);

            return redirect()->route('pricing')
                ->with('warning', 'Simulation: Your subscription was expired. Premium access is now revoked (Scenario AT-014 verified).');
        }

        return back()->with('info', 'No active subscription found to expire.');
    }
}
