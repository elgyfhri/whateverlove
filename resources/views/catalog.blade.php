@extends('layouts.app')

@section('title', isset($category) ? ucfirst($category) : 'Catalog')

@section('content')
<section class="py-16 px-6 md:px-12 max-w-[1440px] mx-auto min-h-screen">
    <div class="mb-10">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">
            @isset($category)
                Collection
            @else
                Full Catalog
            @endisset
        </p>
        <h1 class="font-headline-lg text-3xl md:text-5xl font-extrabold uppercase tracking-tight">
            @isset($category)
                {{ $category }}
            @else
                All Products
            @endisset
        </h1>
        <p class="mt-2 text-sm text-gray-500 font-label-sm uppercase tracking-wider">{{ $products->count() }} items</p>
    </div>

    <div class="flex flex-wrap gap-3 mb-12">
        <a href="{{ route('catalog') }}" class="font-label-sm text-xs uppercase tracking-wider px-5 py-2.5 rounded-full transition {{ !isset($category) ? 'bg-black text-white' : 'bg-[#f8f8f8] text-gray-700 hover:bg-gray-200' }}">
            All
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('catalog.category', $cat->slug) }}" class="font-label-sm text-xs uppercase tracking-wider px-5 py-2.5 rounded-full transition {{ isset($category) && $category === $cat->slug ? 'bg-black text-white' : 'bg-[#f8f8f8] text-gray-700 hover:bg-gray-200' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    @if($products->isEmpty())
        <div class="text-center py-24">
            <div class="text-5xl mb-4">✎</div>
            <h2 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No products yet</h2>
            <p class="mt-2 text-gray-500 text-sm">This collection is coming soon.</p>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <a href="{{ route('product.show', $product) }}" class="group cursor-pointer">
                    <div class="relative aspect-square overflow-hidden bg-[#f8f8f8] mb-4 rounded-lg flex items-center justify-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-4/5 h-4/5 object-contain group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-4/5 h-4/5 flex items-center justify-center text-4xl text-gray-300">♡</div>
                        @endif
                        @if($product->badge === 'sold_out')
                            <div class="absolute top-3 left-3 bg-black text-white px-2 py-0.5 text-[10px] font-bold tracking-wider rounded-sm uppercase">SOLD OUT</div>
                        @elseif($product->badge === 'new')
                            <div class="absolute top-3 left-3 bg-secondary text-white px-2 py-0.5 text-[10px] font-bold tracking-wider rounded-sm uppercase">NEW</div>
                        @endif
                    </div>
                    <h3 class="font-bold text-xs uppercase tracking-wider mb-1">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-500 font-label-sm">IDR {{ number_format((float)$product->price, 0, '.', '.') }}</p>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endsection