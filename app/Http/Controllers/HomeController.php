<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Quote;
use App\Models\ReadingLog;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredBooks = Book::published()
            ->with('category')
            ->latest()
            ->take(6)
            ->get();

        $categories = Category::withCount('books')->get();

        $sampleQuotes = Quote::where('is_favorite', true)
            ->with(['book', 'user'])
            ->latest()
            ->take(3)
            ->get();

        if ($sampleQuotes->isEmpty()) {
            $sampleQuotes = Quote::with(['book', 'user'])->latest()->take(3)->get();
        }

        $stats = [
            'total_books' => Book::published()->count(),
            'total_readers' => User::count(),
            'total_pages_logged' => ReadingLog::sum('pages_read'),
            'categories_count' => Category::count(),
        ];

        return view('welcome', compact('featuredBooks', 'categories', 'sampleQuotes', 'stats'));
    }
}
