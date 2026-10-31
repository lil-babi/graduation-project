<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with(['category', 'reviews'])
            ->latest()
            ->paginate(15);

        return view('admin.books.index', compact('books'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        // SRS FR-093 & FR-094: File uploads shall validate file type and size on server (50MB = 51200KB)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'page_count' => ['required', 'integer', 'min:1'],
            'publication_year' => ['required', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'description' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:51200'], // 50MB
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverFile = $request->file('cover_image');
            $coverName = 'covers/'.Str::uuid().'.'.$coverFile->getClientOriginalExtension();
            $coverPath = $coverFile->storeAs('covers', Str::uuid().'.'.$coverFile->getClientOriginalExtension(), 'public');
        }

        $pdfPath = null;
        if ($request->hasFile('pdf_file')) {
            // SRS FR-095: Files shall be stored using safe generated names; do not trust user-supplied filename
            $pdfFile = $request->file('pdf_file');
            $pdfPath = $pdfFile->storeAs('books/pdfs', Str::uuid().'.pdf', 'public');
        }

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'category_id' => $validated['category_id'],
            'page_count' => (int) $validated['page_count'],
            'publication_year' => (int) $validated['publication_year'],
            'description' => $validated['description'],
            'is_published' => $request->boolean('is_published', true),
            'cover_path' => $coverPath,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('admin.books.index')
            ->with('success', "Book '{$book->title}' has been added to the catalog.");
    }

    public function edit(Book $book): View
    {
        $categories = Category::all();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'page_count' => ['required', 'integer', 'min:1'],
            'publication_year' => ['required', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'description' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $updateData = [
            'title' => $validated['title'],
            'author' => $validated['author'],
            'category_id' => $validated['category_id'],
            'page_count' => (int) $validated['page_count'],
            'publication_year' => (int) $validated['publication_year'],
            'description' => $validated['description'],
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->hasFile('cover_image')) {
            if ($book->cover_path && Storage::disk('public')->exists($book->cover_path)) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $coverFile = $request->file('cover_image');
            $updateData['cover_path'] = $coverFile->storeAs('covers', Str::uuid().'.'.$coverFile->getClientOriginalExtension(), 'public');
        }

        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
                Storage::disk('public')->delete($book->pdf_path);
            }
            $pdfFile = $request->file('pdf_file');
            $updateData['pdf_path'] = $pdfFile->storeAs('books/pdfs', Str::uuid().'.pdf', 'public');
        }

        $book->update($updateData);

        return redirect()->route('admin.books.index')
            ->with('success', "Book '{$book->title}' updated successfully.");
    }

    public function destroy(Book $book): RedirectResponse
    {
        $title = $book->title;

        // Clean up files safely
        if ($book->cover_path && Storage::disk('public')->exists($book->cover_path)) {
            Storage::disk('public')->delete($book->cover_path);
        }
        if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
            Storage::disk('public')->delete($book->pdf_path);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', "Book '{$title}' removed from catalog.");
    }
}
