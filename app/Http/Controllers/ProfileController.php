<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        $categories = Category::all();
        $subscription = $user->activeSubscription();
        $recentTransactions = $user->transactions()->take(5)->get();

        return view('profile.edit', compact('user', 'categories', 'subscription', 'recentTransactions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // SRS FR-016: Explicitly only validate permitted fields. Never allow changing role or subscription from profile!
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'daily_goal_pages' => ['required', 'integer', 'min:1', 'max:1000'],
            'favorite_category' => ['nullable', 'string', 'max:100'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', Password::defaults(), 'confirmed'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'daily_goal_pages' => (int) $validated['daily_goal_pages'],
            'preferences' => array_merge($user->preferences ?? [], [
                'favorite_category' => $validated['favorite_category'] ?? null,
            ]),
        ];

        if (! empty($validated['new_password'])) {
            $updateData['password'] = Hash::make($validated['new_password']);
        }

        $user->update($updateData);

        return back()->with('success', 'Profile and reading preferences updated successfully.');
    }
}
