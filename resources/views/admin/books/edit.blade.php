@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.books.index') }}" class="text-xs text-stone-500 hover:text-stone-900 font-medium">← Back to Catalog List</a>
            <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Edit Book: {{ $book->title }}</h1>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-3xl border border-stone-200/80 shadow-xs">
            <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Book Title *</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $book->title) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                    </div>

                    <div>
                        <label for="author" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Author Name *</label>
                        <input id="author" type="text" name="author" value="{{ old('author', $book->author) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Category *</label>
                        <select id="category_id" name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="page_count" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Page Count *</label>
                        <input id="page_count" type="number" name="page_count" min="1" value="{{ old('page_count', $book->page_count) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                    </div>

                    <div>
                        <label for="publication_year" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Publication Year *</label>
                        <input id="publication_year" type="number" name="publication_year" min="1000" max="{{ date('Y') + 1 }}" value="{{ old('publication_year', $book->publication_year) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Synopsis / Description *</label>
                    <textarea id="description" name="description" rows="5" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white font-serif">{{ old('description', $book->description) }}</textarea>
                </div>

                <!-- File Replacement -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-stone-100">
                    <div>
                        <label for="cover_image" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">
                            Replace Cover Image
                        </label>
                        @if($book->cover_path)
                            <div class="text-[11px] text-emerald-700 mb-1">Current cover file exists</div>
                        @endif
                        <input id="cover_image" type="file" name="cover_image" accept="image/*"
                            class="w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-100 file:text-stone-800 hover:file:bg-stone-200">
                    </div>

                    <div>
                        <label for="pdf_file" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">
                            Replace PDF File (Max 50MB)
                        </label>
                        @if($book->pdf_path)
                            <div class="text-[11px] text-emerald-700 mb-1">Current PDF file exists</div>
                        @endif
                        <input id="pdf_file" type="file" name="pdf_file" accept=".pdf,application/pdf"
                            class="w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-900 hover:file:bg-brand-100">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-stone-700">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $book->is_published) ? 'checked' : '' }} class="rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                        <span>Published to Catalog</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 text-xs font-semibold hover:bg-stone-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                        Update Book Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
