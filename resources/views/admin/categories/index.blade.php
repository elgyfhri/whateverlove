@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Organization</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Categories</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $categories->count() }} categories</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Create form -->
    <div class="bg-white rounded-2xl border border-gray-100 p-8 h-fit lg:sticky lg:top-32 lg:self-start">
        <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-6">Add New Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Category Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
                    placeholder="e.g. Jackets">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="image" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Category Image</label>
                <input id="image" type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 2MB. Dipakai di section "Shop by Categories" di halaman home.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <p class="text-xs text-gray-400 font-label-sm -mt-2">Slug dibuat otomatis dari nama.</p>
            <button type="submit"
                class="w-full px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                + Create Category
            </button>
        </form>
    </div>

    <!-- Categories list -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden h-fit">
        @if($categories->isEmpty())
            <div class="p-16 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-4 block">category</span>
                <h3 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No categories yet</h3>
                <p class="mt-2 text-gray-500 text-sm">Create your first category on the left.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#f8f8f8] text-left font-label-sm text-[10px] uppercase tracking-widest text-gray-500">
                            <th class="px-6 py-4 font-bold">Name</th>
                            <th class="px-6 py-4 font-bold">Slug</th>
                            <th class="px-6 py-4 font-bold">Products</th>
                            <th class="px-6 py-4 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($categories as $category)
                            <tr class="hover:bg-[#fafafa] transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#f8f8f8] flex-shrink-0">
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300"><span class="material-symbols-outlined text-lg">category</span></div>
                                            @endif
                                        </div>
                                        <p class="font-bold text-xs uppercase tracking-wider text-gray-900">{{ $category->name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-label-sm text-xs text-gray-500">/{{ $category->slug }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-[#f8f8f8] text-gray-600 px-3 py-1 rounded-full">{{ $category->products_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="px-4 py-2 bg-[#f8f8f8] hover:bg-gray-200 text-gray-700 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
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