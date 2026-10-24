@extends('layouts.app')

@section('title', 'WHATEVERLOVE | A Canvas of Expression')

@section('content')
  <!-- Hero Section -->
  <section class="relative h-screen w-full flex items-center justify-center overflow-hidden -mt-20">
    <div class="absolute inset-0 z-0">
      <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ str_starts_with($heroImage, 'assets/') ? asset($heroImage) : asset('storage/' . $heroImage) }}')"></div>
      <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-white"></div>
    </div>
    <div class="relative z-10 text-center px-4">
      <h1 class="text-white font-display-xl text-[clamp(2.25rem,12vw,4.5rem)] leading-none font-extrabold tracking-[-0.04em] drop-shadow-md uppercase whitespace-nowrap">
        WHATEVERLOVE
      </h1>
    </div>
  </section>

  <!-- Featured Products Section -->
  <section class="py-16 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="flex justify-between items-end mb-10">
      <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">From the catalog</p>
        <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight">Featured Products</h2>
      </div>
      <a href="{{ route('catalog') }}" class="hidden sm:inline-block font-label-sm text-xs uppercase tracking-wider text-gray-500 hover:text-black transition-colors">View All →</a>
    </div>

    @if($featured->isEmpty())
      <div class="text-center py-16">
        <div class="text-5xl mb-4">✎</div>
        <h3 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No products yet</h3>
        <p class="mt-2 text-gray-500 text-sm">The collection is coming soon.</p>
      </div>
    @else
      <div class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar -mx-6 md:-mx-12 px-6 md:px-12">
        @foreach($featured as $product)
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

  <!-- Shop by Categories Section -->
  <section class="py-16 bg-[#f8f8f8]">
    <div class="px-6 md:px-12 max-w-[1440px] mx-auto">
      <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight mb-10">Shop by Categories</h2>
      <div class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory no-scrollbar -mx-6 md:-mx-12 px-6 md:px-12">
        @foreach($categories as $category)
          <a href="{{ route('catalog.category', $category->slug) }}" class="relative aspect-[3/4] group overflow-hidden cursor-pointer rounded-lg snap-start shrink-0 w-[200px] sm:w-[280px]">
            <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500" style="background-image: url('{{ $category->cover_url }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <div class="absolute bottom-6 left-6 flex items-center gap-2 text-white">
              <span class="font-headline-lg text-lg uppercase font-bold tracking-wider">{{ $category->name }}</span>
              <span class="material-symbols-outlined text-sm">arrow_outward</span>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-20 px-6 md:px-12 max-w-4xl mx-auto text-center">
    <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight mb-6">ABOUT WHATEVERLOVE</h2>
    <p class="text-sm md:text-base text-gray-600 leading-relaxed max-w-3xl mx-auto">
      {{ $aboutText }}
    </p>
  </section>

  <!-- Bottom Grid -->
  <section class="grid grid-cols-1 md:grid-cols-3 h-[450px] w-full">
    @foreach($sections as $section)
      <div class="group relative overflow-hidden flex flex-col justify-end p-8 {{ !$loop->last ? 'border-r border-gray-100' : '' }}">
        <div class="absolute inset-0 bg-cover bg-center grayscale group-hover:scale-105 transition-all duration-700" style="background-image: url('{{ $section->cover_url }}')"></div>
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 text-white">
          <h4 class="font-headline-lg uppercase text-xl font-bold mb-2">{{ $section->title }}</h4>
          <a class="font-label-sm text-xs border-b border-white pb-0.5 uppercase tracking-widest hover:text-gray-300 transition-colors" href="{{ $section->link ?: '#' }}">{{ $section->link_text ?: 'VIEW MORE' }}</a>
        </div>
      </div>
    @endforeach
  </section>
@endsection