@extends('layouts.admin')

@section('title', 'Manage Products')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Catalog</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Products</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $products->count() }} items</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="self-start md:self-auto inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
        <span class="material-symbols-outlined text-sm">add</span> Add Product
    </a>
</div>

@if($products->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
        <span class="material-symbols-outlined text-5xl text-gray-300 mb-4 block">inventory_2</span>
        <h2 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No products yet</h2>
        <p class="mt-2 text-gray-500 text-sm">Start building your collection.</p>
        <a href="{{ route('admin.products.create') }}" class="inline-block mt-6 px-6 py-3 bg-black text-white rounded-full text-xs font-bold uppercase tracking-widest hover:opacity-80 transition">
            Add Your First Product
        </a>
    </div>
@else
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f8f8f8] text-left font-label-sm text-[10px] uppercase tracking-widest text-gray-500">
                        <th class="px-6 py-4 font-bold">Product</th>
                        <th class="px-6 py-4 font-bold">Category</th>
                        <th class="px-6 py-4 font-bold">Price</th>
                        <th class="px-6 py-4 font-bold">Weight</th>
                        <th class="px-6 py-4 font-bold">Stock</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($products as $product)
                        <tr class="hover:bg-[#fafafa] transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('product.show', $product) }}" target="_blank" class="flex items-center gap-4 group">
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#f8f8f8] flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">♡</div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-xs uppercase tracking-wider text-gray-900 group-hover:underline">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400 font-label-sm">{{ $product->created_at->format('d M Y') }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-[#f8f8f8] text-gray-600 px-3 py-1 rounded-full">{{ $product->category_label }}</span>
                                    @if($product->article)
                                        <a href="{{ route('admin.articles.edit', $product->article) }}" class="font-label-sm text-[10px] uppercase tracking-widest bg-[#111111] text-white px-3 py-1 rounded-full hover:opacity-80 transition">{{ $product->article->name }}</a>
                                    @endif
                                    @if($product->badge === 'new')
                                        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-secondary text-white px-3 py-1 rounded-full">NEW</span>
                                    @elseif($product->badge === 'sold_out')
                                        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-black text-white px-3 py-1 rounded-full">SOLD OUT</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-label-sm text-gray-900">
                                IDR {{ number_format((float)$product->price, 0, '.', '.') }}
                            </td>
                            <td class="px-6 py-4 font-label-sm text-gray-500">
                                {{ (int) $product->weight }} g
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-label-sm text-[10px] uppercase tracking-widest bg-[#f8f8f8] text-gray-600 px-3 py-1 rounded-full">
                                    {{ (int) $product->sizes_sum_stock }} unit
                                </span>
                                @if($product->sizes->count() === 0)
                                    <span class="ml-1 font-label-sm text-[10px] uppercase tracking-widest bg-amber-100 text-amber-700 px-3 py-1 rounded-full">Ukuran belum di-set</span>
                                @elseif((int) $product->sizes_sum_stock === 0)
                                    <span class="ml-1 font-label-sm text-[10px] uppercase tracking-widest bg-black text-white px-3 py-1 rounded-full">Habis</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($product->is_active)
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-green-50 text-green-600 px-3 py-1 rounded-full">Active</span>
                                @else
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Hidden</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 bg-[#f8f8f8] hover:bg-gray-200 text-gray-700 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product permanently?')">
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
    </div>
@endif
@endsection