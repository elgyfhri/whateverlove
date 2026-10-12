@extends('layouts.admin')

@section('title', 'Articles')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Collections & themes</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Articles</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $articles->count() }} articles</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Create form --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-8 h-fit lg:sticky lg:top-32 lg:self-start">
        <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-6">Add New Article</h2>
        <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Article Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
                    placeholder="e.g. Halloween Drop">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors resize-none"
                    placeholder="What is this article about?">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="image" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Cover Image</label>
                <input id="image" type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 2MB.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <p class="text-xs text-gray-400 font-label-sm -mt-2">Slug dibuat otomatis dari nama.</p>
            <button type="submit"
                class="w-full px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                + Create Article
            </button>
        </form>
    </div>

    {{-- Articles list --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden h-fit">
        @if($articles->isEmpty())
            <div class="p-16 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-4 block">menu_book</span>
                <h3 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No articles yet</h3>
                <p class="mt-2 text-gray-500 text-sm">Create your first article on the left.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#f8f8f8] text-left font-label-sm text-[10px] uppercase tracking-widest text-gray-500">
                            <th class="px-6 py-4 font-bold">Name</th>
                            <th class="px-6 py-4 font-bold">Slug</th>
                            <th class="px-6 py-4 font-bold">Products</th>
                            <th class="px-6 py-4 font-bold">Status</th>
                            <th class="px-6 py-4 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($articles as $article)
                            <tr class="hover:bg-[#fafafa] transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#f8f8f8] flex-shrink-0">
                                            @if($article->image)
                                                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300"><span class="material-symbols-outlined text-lg">image</span></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs uppercase tracking-wider text-gray-900">{{ $article->name }}</p>
                                            @if($article->description)
                                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1 max-w-[220px]">{{ $article->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-label-sm text-xs text-gray-500">/{{ $article->slug }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-[#f8f8f8] text-gray-600 px-3 py-1 rounded-full">{{ $article->products_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($article->is_active)
                                        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-green-50 text-green-600 px-3 py-1 rounded-full">Active</span>
                                    @else
                                        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.articles.edit', $article) }}" class="px-4 py-2 bg-[#f8f8f8] hover:bg-gray-200 text-gray-700 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Delete this article? Products still linked to it will be unassigned.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection