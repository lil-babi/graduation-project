@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-stone-500 hover:text-stone-900 font-medium">← Back to Admin Console</a>
            <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Book Categories Management</h1>
            <p class="text-sm text-stone-600 mt-1">Configure catalog categories and genres (SRS FR-024).</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Add Category Form -->
            <div class="lg:col-span-4">
                <div class="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-xs sticky top-24">
                    <h2 class="font-serif text-lg font-bold text-stone-900 mb-1">Create Category</h2>
                    <p class="text-xs text-stone-500 mb-5">Add a new genre classification to the catalog.</p>

                    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Category Name *</label>
                            <input id="name" type="text" name="name" required placeholder="e.g. Science Fiction" value="{{ old('name') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1">Description (Optional)</label>
                            <textarea id="description" name="description" rows="3" placeholder="Brief scope of the category..."
                                class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-stone-900 bg-white">{{ old('description') }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-stone-900 text-white font-semibold text-xs hover:bg-stone-800 transition shadow-xs">
                            Create Category
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Categories List Table -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                        <h2 class="font-serif text-lg font-bold text-stone-900">Current Categories ({{ $categories->count() }})</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider font-semibold border-b border-stone-200/60">
                                <tr>
                                    <th class="py-3.5 px-6">Name</th>
                                    <th class="py-3.5 px-6">Slug</th>
                                    <th class="py-3.5 px-6">Assigned Books</th>
                                    <th class="py-3.5 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-stone-700">
                                @foreach($categories as $category)
                                    <tr class="hover:bg-stone-50/60">
                                        <td class="py-4 px-6 font-bold text-stone-900">
                                            {{ $category->name }}
                                            @if($category->description)
                                                <p class="text-[11px] text-stone-400 font-normal mt-0.5">{{ $category->description }}</p>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-mono text-[11px] text-stone-500">
                                            {{ $category->slug }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-stone-900">
                                            {{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            @if($category->books_count === 0)
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline font-semibold text-[11px]">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-stone-400 text-[10px]">In Use (Protected)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
