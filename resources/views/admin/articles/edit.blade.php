@extends('layouts.admin')

@section('title', 'Edit Article')

@section('content')
<div class="max-w-2xl">
    <div class="mb-10">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Collections & themes</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Edit Article</h1>
        <p class="mt-2 text-sm text-gray-500">Update the article name, description, and status.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Article Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $article->name) }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full bg-transparent border-b-2 @error('description') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors resize-none">{{ old('description', $article->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Slug</label>
                <input type="text" value="/{{ $article->slug }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500">
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Cover Image</label>
                @if($article->image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->name }}" class="w-48 aspect-[4/3] object-cover rounded-xl border border-gray-200">
                        <p class="mt-2 text-xs text-gray-400 font-label-sm">Current cover. Upload a new one to replace it.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 3MB.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="flex items-center gap-3 font-label-sm text-xs uppercase tracking-widest text-gray-700">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $article->is_active) ? 'checked' : '' }} class="w-4 h-4 text-black rounded focus:ring-black">
                    Active (tampil di halaman publik)
                </label>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="px-8 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Save Changes
                </button>
                <a href="{{ route('admin.articles.index') }}" class="px-8 py-3.5 bg-[#f8f8f8] text-gray-700 rounded-full font-label-sm text-xs uppercase tracking-wider hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection