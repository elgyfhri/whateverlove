@extends('layouts.app')

@section('title', $article->name)

@section('content')
{{-- Breadcrumb --}}
<section class="pt-16 px-6 md:px-12 max-w-[1440px] mx-auto">
    <nav class="flex flex-wrap gap-2 text-xs font-label-sm uppercase tracking-wider text-gray-400 mb-8">
        <a href="/" class="hover:text-black transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('article') }}" class="hover:text-black transition-colors">Article</a>
        <span>/</span>
        <span class="text-gray-900">{{ $article->name }}</span>
    </nav>
</section>

{{-- Article header --}}
<section class="px-6 md:px-12 max-w-[1440px] mx-auto pb-12">
    <div class="relative aspect-[4/3] md:aspect-[21/9] overflow-hidden rounded-2xl bg-[#f8f8f8] mb-10">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $article->image ? asset('storage/' . $article->image) : asset('assets/hero.jpg') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-8 md:p-14 text-white">
            <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-300 mb-3">Collection</p>
            <h1 class="font-headline-lg text-3xl md:text-5xl font-extrabold uppercase tracking-tight">{{ $article->name }}</h1>
            @if($article->description)
                <p class="mt-3 text-sm md:text-base text-gray-300 max-w-xl leading-relaxed">{{ $article->description }}</p>
            @endif
        </div>
    </div>
</section>

{{-- Products --}}
<section class="pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="flex justify-between items-end mb-10">
        <div>
            <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">The pieces</p>
            <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight">Products</h2>
        </div>
        <span class="font-label-sm text-xs uppercase tracking-wider text-gray-400">{{ $products->count() }} products</span>
    </div>

    @if($products->isEmpty())
        <div class="py-16 text-center rounded-2xl border border-gray-100">
            <p class="text-sm text-gray-500">Belum ada produk di artikel ini.</p>
        </div>
    @else
        <div class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar">
            @foreach($products as $product)
                <a href="{{ route('product.show', $product) }}" class="group cursor-pointer snap-start shrink-0 w-[160px] sm:w-[200px]">
                    <div class="relative aspect-square overflow-hidden bg-[#f8f8f8] mb-4 rounded-lg flex items-center justify-center">
                        @if($product->image)
                            <img alt="{{ $product->name }}" class="w-4/5 h-4/5 object-contain group-hover:scale-105 transition-transform duration-500" src="{{ asset('storage/' . $product->image) }}" />
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