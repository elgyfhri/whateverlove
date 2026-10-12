@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="max-w-2xl">
    <div class="mb-10">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Catalog</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Add Product</h1>
        <p class="mt-2 text-sm text-gray-500">Fill in the details to publish a new item.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Product Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
                    placeholder="T-Shirt Whatever Classic">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Category <span class="text-secondary">*</span></label>
                <select id="category" name="category" required
                    class="w-full px-4 py-3 border @error('category') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm bg-white">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ old('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400 font-label-sm">
                    <a href="{{ route('admin.categories.index') }}" class="text-black underline">Manage categories →</a>
                </p>
                @error('category') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="article_id" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Article</label>
                <select id="article_id" name="article_id"
                    class="w-full px-4 py-3 border @error('article_id') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm bg-white">
                    <option value="">No article</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}" {{ old('article_id') == $article->id ? 'selected' : '' }}>{{ $article->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400 font-label-sm">
                    <a href="{{ route('admin.articles.index') }}" class="text-black underline">Manage articles →</a>
                </p>
                @error('article_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Badge Label</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 font-label-sm text-xs uppercase tracking-wider text-gray-700">
                        <input type="radio" name="badge" value="" {{ old('badge') === null ? 'checked' : '' }} class="text-black focus:ring-black">
                        None
                    </label>
                    <label class="flex items-center gap-2 font-label-sm text-xs uppercase tracking-wider text-gray-700">
                        <input type="radio" name="badge" value="new" {{ old('badge') === 'new' ? 'checked' : '' }} class="text-secondary focus:ring-secondary">
                        <span class="bg-secondary text-white px-2 py-0.5 text-[10px] font-bold rounded-sm">NEW</span>
                    </label>
                    <label class="flex items-center gap-2 font-label-sm text-xs uppercase tracking-wider text-gray-700">
                        <input type="radio" name="badge" value="sold_out" {{ old('badge') === 'sold_out' ? 'checked' : '' }} class="text-black focus:ring-black">
                        <span class="bg-black text-white px-2 py-0.5 text-[10px] font-bold rounded-sm">SOLD OUT</span>
                    </label>
                </div>
                @error('badge') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Price (IDR) <span class="text-secondary">*</span></label>
                <input id="price" type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                    class="w-full bg-transparent border-b-2 @error('price') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
                    placeholder="450000">
                @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="weight" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Weight (gram) <span class="text-secondary">*</span></label>
                <input id="weight" type="number" name="weight" value="{{ old('weight', 250) }}" required min="1" max="100000"
                    class="w-full bg-transparent border-b-2 @error('weight') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
                    placeholder="250">
                @error('weight') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-4 py-3 border @error('description') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm resize-y"
                    placeholder="Short description of the product...">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Product Image</label>
                <input id="image" type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 3MB.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="images" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Gallery Photos <span class="text-gray-400 font-normal normal-case">(front, back, size chart, model, etc.)</span></label>
                <input id="images" type="file" name="images[]" accept="image/*" multiple
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">Bisa pilih beberapa foto sekaligus. JPG, PNG, WebP. Max 3MB per foto.</p>
                @error('images') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('images.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            @include('admin.products._sizes', ['existingSizes' => collect()])

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="px-8 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Publish Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3.5 bg-[#f8f8f8] text-gray-700 rounded-full font-label-sm text-xs uppercase tracking-wider hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection