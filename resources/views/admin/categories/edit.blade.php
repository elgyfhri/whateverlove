@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-2xl">
    <div class="mb-10">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Organization</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Edit Category</h1>
        <p class="mt-2 text-sm text-gray-500">Update the category name and its slug.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Category Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Slug</label>
                <input type="text" value="/{{ $category->slug }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">Slug akan diperbarui otomatis dari nama. Produk yang memakai kategori ini ikut terpindahkan.</p>
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Category Image</label>
                @if($category->image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-48 aspect-[3/4] object-cover rounded-xl border border-gray-200">
                        <p class="mt-2 text-xs text-gray-400 font-label-sm">Current image. Upload a new one to replace it.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 2MB. Dipakai di section "Shop by Categories" di halaman home.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="px-8 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Save Changes
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-8 py-3.5 bg-[#f8f8f8] text-gray-700 rounded-full font-label-sm text-xs uppercase tracking-wider hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection