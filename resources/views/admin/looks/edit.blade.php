@extends('layouts.admin')

@section('title', 'Edit Look')

@section('content')
<div class="max-w-2xl">
    <div class="mb-10">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Editorial stories</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Edit Look</h1>
        <p class="mt-2 text-sm text-gray-500">Update the look name, description, image, and featured products.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.looks.update', $look) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Look Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $look->name) }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full bg-transparent border-b-2 @error('description') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors resize-none">{{ old('description', $look->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Slug</label>
                <input type="text" value="/{{ $look->slug }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500">
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Cover Image</label>
                @if($look->image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $look->image) }}" alt="{{ $look->name }}" class="w-48 aspect-[3/4] object-cover rounded-xl border border-gray-200">
                        <p class="mt-2 text-xs text-gray-400 font-label-sm">Current cover. Upload a new one to replace it.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 2MB.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Look Photos</label>

                @if($look->lookImages->isNotEmpty())
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                        @foreach($look->lookImages as $galleryImage)
                            <label class="relative group cursor-pointer">
                                <img src="{{ asset('storage/' . $galleryImage->image) }}" alt="Look photo" class="w-full aspect-[3/4] object-cover rounded-xl border border-gray-200 group-hover:opacity-60 transition">
                                <input type="checkbox" name="remove_images[]" value="{{ $galleryImage->id }}" class="peer sr-only">
                                <span class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center rounded-full bg-white/90 text-xs font-bold text-red-500 opacity-0 peer-checked:opacity-100 group-hover:opacity-100 transition">✕</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 font-label-sm mb-3">Centang foto yang ingin dihapus. Setelah disimpan, urutan foto tidak bisa diubah.</p>
                @endif

                <input id="images" type="file" name="images[]" accept="image/*" multiple
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">Tambah foto baru — tampil berurutan di slider. JPG, PNG, WebP. Max 2MB per foto.</p>
                @error('images') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('images.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('remove_images') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Featured Products</label>
                <div class="max-h-56 overflow-y-auto border border-gray-200 rounded-xl p-3 space-y-2">
                    @forelse($products as $product)
                        <label class="flex items-center gap-3 text-sm cursor-pointer hover:bg-gray-50 rounded-lg p-2 transition">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                {{ in_array($product->id, $look->products->pluck('id')->all()) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-black focus:ring-black shrink-0">
                            <div class="w-9 h-9 rounded-lg overflow-hidden bg-[#f8f8f8] flex-shrink-0 flex items-center justify-center">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-300 text-xs">♡</span>
                                @endif
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-700">{{ $product->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400 font-label-sm">Buat produk dulu untuk dimasukkan ke look.</p>
                    @endforelse
                </div>
                @error('product_ids') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="flex items-center gap-3 font-label-sm text-xs uppercase tracking-widest text-gray-700">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $look->is_active) ? 'checked' : '' }} class="w-4 h-4 text-black rounded focus:ring-black">
                    Active (tampil di halaman publik)
                </label>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="px-8 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Save Changes
                </button>
                <a href="{{ route('admin.looks.index') }}" class="px-8 py-3.5 bg-[#f8f8f8] text-gray-700 rounded-full font-label-sm text-xs uppercase tracking-wider hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection