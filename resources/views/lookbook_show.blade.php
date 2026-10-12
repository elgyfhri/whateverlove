@extends('layouts.app')

@section('title', $look->name)

@section('content')
{{-- Breadcrumb --}}
<section class="pt-16 px-6 md:px-12 max-w-[1440px] mx-auto">
    <nav class="flex flex-wrap gap-2 text-xs font-label-sm uppercase tracking-wider text-gray-400 mb-8">
        <a href="/" class="hover:text-black transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('lookbook') }}" class="hover:text-black transition-colors">Lookbook</a>
        <span>/</span>
        <span class="text-gray-900">{{ $look->name }}</span>
    </nav>
</section>

{{-- Hero slider --}}
@php
    $slideImages = $look->lookImages->map(fn($img) => asset('storage/' . $img->image));
    if ($slideImages->isEmpty()) {
        $slideImages = collect([$look->cover_url]);
    }
@endphp
<section class="pb-10">
    <div id="lookSlider" class="relative">
        <div class="relative bg-[#111111] overflow-hidden select-none">
            <div class="aspect-[4/3] md:aspect-[21/10]">
                <div id="sliderTrack" class="flex h-full w-full transition-transform duration-300 ease-out">
                    @foreach($slideImages as $slideImage)
                        <img src="{{ $slideImage }}" alt="{{ $look->name }}" class="w-full h-full flex-shrink-0 object-cover" draggable="false">
                    @endforeach
                </div>
            </div>

            @if($slideImages->count() > 1)
                <span id="sliderCounter" class="absolute top-4 right-4 bg-black/60 text-white text-[10px] font-bold px-2.5 py-1 rounded-full font-label-sm">1/{{ $slideImages->count() }}</span>

                <button id="sliderPrev" type="button" aria-label="Previous photo"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 flex items-center justify-center rounded-full bg-white/85 hover:bg-white shadow text-gray-800 text-xl border border-gray-100 transition disabled:opacity-0">‹</button>
                <button id="sliderNext" type="button" aria-label="Next photo"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 flex items-center justify-center rounded-full bg-white/85 hover:bg-white shadow text-gray-800 text-xl border border-gray-100 transition disabled:opacity-0">›</button>
            @endif
        </div>

        <div class="max-w-[1440px] mx-auto px-6 md:px-12">
            {{-- Thumbnail strip --}}
            @if($slideImages->count() > 1)
                <div id="thumbStrip" class="mt-6 flex gap-3 overflow-x-auto pb-2 no-scrollbar">
                    @foreach($slideImages as $i => $slideImage)
                        <button data-thumb data-index="{{ $i }}" type="button" aria-label="Go to photo {{ $i + 1 }}"
                            class="shrink-0 w-20 md:w-28 aspect-[3/2] overflow-hidden rounded-lg border-2 border-transparent transition focus:outline-none">
                            <img src="{{ $slideImage }}" alt="" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Look title --}}
            <div class="mt-10">
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-3">Lookbook</p>
                <h1 class="font-headline-lg text-3xl md:text-5xl font-extrabold uppercase tracking-tight">{{ $look->name }}</h1>
                @if($look->description)
                    <p class="mt-3 text-sm md:text-base text-gray-500 max-w-2xl leading-relaxed">{{ $look->description }}</p>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('article') }}"
                    class="px-8 py-3.5 border-2 border-gray-300 text-gray-700 uppercase text-xs font-bold tracking-widest rounded-full hover:border-black hover:text-black transition">
                    View Issue
                </a>
                <a href="#products"
                    class="px-8 py-3.5 border-2 border-gray-300 text-gray-700 uppercase text-xs font-bold tracking-widest rounded-full hover:border-black hover:text-black transition">
                    View Products
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Products --}}
<section id="products" class="pb-24 px-6 md:px-12 max-w-[1440px] mx-auto scroll-mt-28">
    <div class="flex justify-between items-end mb-10">
        <div>
            <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Shop the look</p>
            <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight">The Pieces</h2>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="py-16 text-center rounded-2xl border border-gray-100">
            <p class="text-sm text-gray-500">Belum ada produk di look ini.</p>
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

{{-- Other lookbook --}}
@php
    $others = \App\Models\Look::with('lookImages')
        ->where('is_active', true)
        ->where('id', '!=', $look->id)
        ->latest()
        ->take(8)
        ->get();
@endphp
@if($others->isNotEmpty())
    <section class="pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
        <div class="flex justify-between items-end mb-10">
            <div>
                <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Keep exploring</p>
                <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight">Other Lookbook</h2>
            </div>
            <a href="{{ route('lookbook') }}" class="font-label-sm text-xs uppercase tracking-wider hover:underline">View all →</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10">
            @foreach($others as $other)
                <a href="{{ route('lookbook.show', $other) }}" class="group cursor-pointer block">
                    <div class="relative aspect-[3/2] overflow-hidden rounded-lg bg-[#f8f8f8]">
                        <img src="{{ $other->cover_url }}" alt="{{ $other->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    </div>
                    <h3 class="mt-4 font-headline-lg text-sm md:text-base font-extrabold uppercase tracking-tight text-gray-900 group-hover:underline">
                        {{ $other->name }}
                    </h3>
                </a>
            @endforeach
        </div>
    </section>
@endif

<script>
    (function () {
        const track = document.getElementById('sliderTrack');
        if (!track) return;

        const total = track.children.length;
        let index = 0;
        let startX = null;

        const counter = document.getElementById('sliderCounter');
        const prev = document.getElementById('sliderPrev');
        const next = document.getElementById('sliderNext');
        const thumbStrip = document.getElementById('thumbStrip');
        const thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-thumb]'));

        function scrollThumbIntoView(i) {
            const thumb = thumbs[i];
            if (!thumb || !thumbStrip) return;
            thumbStrip.scrollTo({
                left: thumb.offsetLeft - thumbStrip.clientWidth / 2 + thumb.clientWidth / 2,
                behavior: 'smooth'
            });
        }

        function goTo(i) {
            index = Math.max(0, Math.min(total - 1, i));
            track.style.transform = 'translateX(-' + (index * 100) + '%)';
            if (counter) counter.textContent = (index + 1) + '/' + total;
            if (prev) prev.disabled = index === 0;
            if (next) next.disabled = index === total - 1;
            thumbs.forEach(function (t, ti) {
                t.classList.toggle('border-transparent', ti !== index);
                t.classList.toggle('border-black', ti === index);
            });
            scrollThumbIntoView(index);
        }

        thumbs.forEach(function (t, i) {
            t.addEventListener('click', function () { goTo(i); });
        });

        const holder = track.parentElement;

        holder.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
        holder.addEventListener('touchmove', function (e) {
            if (startX === null) return;
            const dx = e.touches[0].clientX - startX;
            track.style.transition = 'none';
            track.style.transform = 'translateX(calc(' + (-index * 100) + '% + ' + dx + 'px))';
        }, { passive: true });
        holder.addEventListener('touchend', function (e) {
            if (startX === null) return;
            const endX = e.changedTouches[0].clientX;
            const dx = endX - startX;
            startX = null;
            track.style.transition = 'transform 300ms ease-out';
            if (dx < -40) goTo(index + 1);
            else if (dx > 40) goTo(index - 1);
            else goTo(index);
        });

        if (prev) prev.addEventListener('click', function () { goTo(index - 1); });
        if (next) next.addEventListener('click', function () { goTo(index + 1); });

        goTo(0);
    })();
</script>
@endsection