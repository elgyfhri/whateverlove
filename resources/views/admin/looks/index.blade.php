@extends('layouts.admin')

@section('title', 'Looks')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Editorial stories</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Lookbook</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $looks->count() }} looks</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Create form --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-8 h-fit lg:sticky lg:top-32 lg:self-start">
        <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-6">Add New Look</h2>
        <form method="POST" action="{{ route('admin.looks.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Look Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
                    placeholder="e.g. Monochrome Summer">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors resize-none"
                    placeholder="What mood does this look capture?">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="image" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Cover Image</label>
                <input id="image" type="file" name="image" accept="image/*"
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 2MB. Dipakai sebagai cover di grid lookbook.</p>
                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="images" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Look Photos <span class="text-secondary">*</span></label>
                <input id="images" type="file" name="images[]" accept="image/*" multiple required
                    class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                <p class="mt-2 text-xs text-gray-400 font-label-sm">Bisa pilih beberapa foto sekaligus — ini yang tampil di slider. JPG, PNG, WebP. Max 2MB per foto.</p>
                @error('images') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                @error('images.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Featured Products</label>
                <div class="max-h-56 overflow-y-auto border border-gray-200 rounded-xl p-3 space-y-2">
                    @forelse($products as $product)
                        <label class="flex items-center gap-3 text-sm cursor-pointer hover:bg-gray-50 rounded-lg p-2 transition">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}
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
            <p class="text-xs text-gray-400 font-label-sm -mt-2">Slug dibuat otomatis dari nama.</p>
            <button type="submit"
                class="w-full px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                + Create Look
            </button>
        </form>
    </div>

    {{-- Looks list --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden h-fit">
        @if($looks->isEmpty())
            <div class="p-16 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-300 mb-4 block">auto_awesome</span>
                <h3 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No looks yet</h3>
                <p class="mt-2 text-gray-500 text-sm">Create your first look on the left.</p>
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
                        @foreach($looks as $look)
                            <tr class="hover:bg-[#fafafa] transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#f8f8f8] flex-shrink-0">
                                            @if($look->image)
                                                <img src="{{ asset('storage/' . $look->image) }}" alt="{{ $look->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300"><span class="material-symbols-outlined text-lg">image</span></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-xs uppercase tracking-wider text-gray-900">{{ $look->name }}</p>
                                            @if($look->description)
                                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1 max-w-[220px]">{{ $look->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-label-sm text-xs text-gray-500">/{{ $look->slug }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-[#f8f8f8] text-gray-600 px-3 py-1 rounded-full">{{ $look->products_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($look->is_active)
                                        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-green-50 text-green-600 px-3 py-1 rounded-full">Active</span>
                                    @else
                                        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Hidden</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.looks.edit', $look) }}" class="px-4 py-2 bg-[#f8f8f8] hover:bg-gray-200 text-gray-700 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.looks.destroy', $look) }}" onsubmit="return confirm('Delete this look? This removes it from the lookbook.')">
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



