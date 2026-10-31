<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categorySlug = $request->query('category', 'all');

        $categories = Category::withCount('books')->get();

        $books = Book::published()
            ->with('category')
            ->search($search)
            ->category($categorySlug)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('books.index', compact('books', 'categories', 'search', 'categorySlug'));
    }

    public function show(Book $book): View
    {
        if (! $book->is_published && (! Auth::check() || ! Auth::user()->isAdmin())) {
            abort(404, 'Book not found.');
        }

        $book->load(['category', 'reviews.user', 'quotes']);

        $user = Auth::user();
        $userBook = $user ? $user->userBooks()->where('book_id', $book->id)->first() : null;
        $userReview = $user ? $book->reviews->firstWhere('user_id', $user->id) : null;

        $relatedBooks = Book::published()
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'userBook', 'userReview', 'relatedBooks'));
    }

    public function read(Book $book): View|Response
    {
        if (! $book->is_published && (! Auth::check() || ! Auth::user()->isAdmin())) {
            abort(404, 'Book not found.');
        }

        if (empty($book->pdf_path)) {
            return redirect()->route('books.show', $book)
                ->with('warning', 'A digital PDF file is not currently available for this book.');
        }

        // Check if PDF exists in public or storage
        $pdfExists = Storage::disk('public')->exists($book->pdf_path) || file_exists(public_path($book->pdf_path));

        $user = Auth::user();
        $userBook = $user ? $user->userBooks()->where('book_id', $book->id)->first() : null;

        $pdfUrl = Storage::disk('public')->exists($book->pdf_path)
            ? asset('storage/'.$book->pdf_path)
            : asset($book->pdf_path);

        return view('books.read', compact('book', 'userBook', 'pdfUrl', 'pdfExists'));
    }
}
