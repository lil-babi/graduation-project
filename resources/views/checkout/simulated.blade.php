@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-md p-8 sm:p-10">
            <!-- Simulated Mode Warning Banner (SRS FR-111 & FR-112) -->
            <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-950 mb-8">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">⚠️</span>
                    <div>
                        <h2 class="font-bold text-sm uppercase tracking-wider">Demonstration / Simulated Checkout Only</h2>
                        <p class="text-xs mt-1 leading-relaxed text-amber-900">
                            This simulated payment screen fulfills the INSA graduation requirements (SRS Section 7.2 & 12.2).
                            <strong>No real money is transferred. No bank or credit card information is required or processed.</strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-amber-400 text-stone-900 flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-xs">
                    ★
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">Upgrade to BookPlatform Premium</h1>
                <p class="text-xs text-stone-500 mt-1">Confirm your simulated subscription entitlement</p>
            </div>

            <!-- Order Summary Card -->
            <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 mb-8 space-y-4">
                <div class="flex items-center justify-between text-sm">
                    <div>
                        <div class="font-bold text-stone-900">Premium Membership (Monthly)</div>
                        <div class="text-xs text-stone-500">Includes velocity analytics, custom reading plans & all incoming features</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-stone-900 font-serif text-lg">{{ $plan['price'] }} {{ $currency }}</div>
                        <div class="text-[10px] text-stone-500">per month (simulated)</div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 leading-relaxed">
                    <strong>★ Early Adopter Guarantee:</strong> Subscribing grants you full, automatic access to all upcoming Premium features (AI Reading Companion, Voice Narration, and Markdown Note Export) as they launch at no additional charge.
                </div>

                <div class="pt-3 border-t border-stone-200 flex justify-between text-xs text-stone-600">
                    <span>Subscriber Account:</span>
                    <span class="font-semibold text-stone-900">{{ $user->email }}</span>
                </div>

                <div class="flex justify-between text-xs text-stone-600">
                    <span>Access Duration:</span>
                    <span class="font-semibold text-stone-900">30 Calendar Days</span>
                </div>

                <div class="flex justify-between text-xs text-stone-600">
                    <span>Billing Mode:</span>
                    <span class="font-semibold text-amber-700">Simulated Graduation Demonstration</span>
                </div>
            </div>

            <!-- Checkout Confirmation Form -->
            <form method="POST" action="{{ route('subscription.process-simulated') }}" class="space-y-6">
                @csrf

                <div class="p-4 rounded-xl bg-stone-100 border border-stone-200 text-xs">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="confirm_demo" value="1" required class="mt-0.5 rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                        <span class="text-stone-700 leading-relaxed">
                            I understand that this is a simulated transaction demonstration for the INSA graduation project. No real financial charges occur, and this records a simulated transaction in the database for auditor review.
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-stone-900 text-white font-bold text-sm hover:bg-stone-800 transition shadow-sm flex items-center justify-center gap-2">
                    <span>Complete Demonstration Upgrade ({{ $plan['price'] }} {{ $currency }})</span>
                    <span>→</span>
                </button>

                <div class="text-center">
                    <a href="{{ route('pricing') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-800">
                        Cancel and return to Pricing
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
