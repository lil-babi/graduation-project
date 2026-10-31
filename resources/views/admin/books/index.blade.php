@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-stone-500 hover:text-stone-900 font-medium">← Back to Admin Console</a>
                <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Catalog Books Management</h1>
            </div>
            <a href="{{ route('admin.books.create') }}" class="px-4 py-2.5 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                + Add New Book
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider font-semibold border-b border-stone-200/60">
                        <tr>
                            <th class="py-3.5 px-6">Book</th>
                            <th class="py-3.5 px-6">Category</th>
                            <th class="py-3.5 px-6">Pages</th>
                            <th class="py-3.5 px-6">Year</th>
                            <th class="py-3.5 px-6">PDF File</th>
                            <th class="py-3.5 px-6">Published</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @forelse($books as $book)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-14 bg-stone-100 rounded-lg overflow-hidden border border-stone-200 shrink-0">
                                            @if($book->cover_path)
                                                <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full p-1 bg-stone-800 text-white text-[7px] flex items-center justify-center text-center font-bold">
                                                    {{ substr($book->title, 0, 10) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('books.show', $book) }}" class="font-serif font-bold text-sm text-stone-900 hover:text-brand-700">
                                                {{ $book->title }}
                                            </a>
                                            <p class="text-[11px] text-stone-500">{{ $book->author }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-medium text-stone-800">
                                    {{ $book->category->name }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-stone-900">
                                    {{ $book->page_count }}
                                </td>
                                <td class="py-4 px-6 text-stone-500">
                                    {{ $book->publication_year }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($book->pdf_path)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            ✓ PDF Uploaded
                                        </span>
                                    @else
                                        <span class="text-stone-400 italic">None</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if($book->is_published)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-800">Published</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Draft</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.books.edit', $book) }}" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold text-[11px] transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Delete this book and associated files from catalog?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-white border border-red-200 text-red-600 hover:bg-red-50 font-semibold text-[11px] transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-500">
                                    No books in catalog. Click "+ Add New Book" above to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-100">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
