@extends('layouts.app')

@section('title', $product->name)

@section('content')
<section class="py-12 px-6 md:px-12 max-w-[1440px] mx-auto min-h-screen">
    <!-- Breadcrumb -->
    <nav class="flex flex-wrap gap-2 text-xs font-label-sm uppercase tracking-wider text-gray-400 mb-10">
        <a href="/" class="hover:text-black transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('catalog') }}" class="hover:text-black transition-colors">Catalog</a>
        <span>/</span>
        @if($product->category)
            <a href="{{ route('catalog.category', $product->category) }}" class="hover:text-black transition-colors">{{ $product->category }}</a>
            <span>/</span>
        @endif
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">
        <div class="lg:sticky lg:top-24">
            @php
                $slideImages = collect();
                if ($product->image) {
                    $slideImages->push(asset('storage/' . $product->image));
                }
                foreach ($product->productImages as $galleryImage) {
                    $slideImages->push(asset('storage/' . $galleryImage->image));
                }
            @endphp

            @if($slideImages->isNotEmpty())
                <div id="productSlider" class="relative">
                    <div class="aspect-square bg-[#f8f8f8] rounded-2xl overflow-hidden relative select-none">
                        <div id="sliderTrack" class="flex h-full w-full transition-transform duration-300 ease-out">
                            @foreach($slideImages as $slideImage)
                                <img src="{{ $slideImage }}" alt="{{ $product->name }}" class="w-full h-full flex-shrink-0 object-contain" draggable="false">
                            @endforeach
                        </div>

                        @if($slideImages->count() > 1)
                            <span id="sliderCounter" class="absolute top-3 right-3 bg-black/50 text-white text-[10px] font-bold px-2.5 py-1 rounded-full font-label-sm">1/{{ $slideImages->count() }}</span>

                            <button id="sliderPrev" type="button" aria-label="Previous photo"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white/80 hover:bg-white shadow text-gray-800 text-lg border border-gray-100 transition disabled:opacity-0">‹</button>
                            <button id="sliderNext" type="button" aria-label="Next photo"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white/80 hover:bg-white shadow text-gray-800 text-lg border border-gray-100 transition disabled:opacity-0">›</button>

                            <div id="sliderDots" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
                        @endif
                    </div>
                </div>
            @else
                <div class="aspect-square bg-[#f8f8f8] rounded-2xl overflow-hidden flex items-center justify-center">
                    <div class="w-4/5 h-4/5 flex items-center justify-center text-6xl text-gray-300">♡</div>
                </div>
            @endif
        </div>

        <div class="mt-10 lg:mt-0">
            @if($product->category)
                <span class="inline-block bg-secondary text-white px-3 py-1 font-label-sm text-[10px] uppercase tracking-widest rounded-sm mb-5">
                    {{ $product->category_label }}
                </span>
            @endif
            @if($product->article)
                <a href="{{ route('article.show', $product->article) }}" class="inline-block bg-[#111111] text-white px-3 py-1 font-label-sm text-[10px] uppercase tracking-widest rounded-sm mb-5 ml-2 hover:opacity-80 transition">
                    {{ $product->article->name }} ↗
                </a>
            @endif

            <h1 class="font-headline-lg text-3xl md:text-4xl font-extrabold uppercase tracking-tight">{{ $product->name }}</h1>

            <p class="mt-4 text-lg text-gray-500 font-label-sm uppercase tracking-wider">
                IDR <span class="text-black font-bold">{{ number_format((float)$product->price, 0, '.', '.') }}</span>
            </p>

            @if($product->description)
                <div class="mt-8 border-t border-gray-100 pt-8">
                    <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Description</h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line text-sm md:text-base">{{ $product->description }}</p>
                </div>
            @endif

            <div class="mt-8 border-t border-gray-100 pt-8">
                <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-4">Order Now</h2>
                <div class="max-w-md">
                    @php
                        $sizes = $product->sizes;
                        $defaultSize = $sizes->firstWhere('stock', '>', 0)?->size ?? $sizes->first()?->size ?? null;
                        $hasAvailable = $sizes->isNotEmpty() && $sizes->where('stock', '>', 0)->isNotEmpty();
                    @endphp
                    <form method="POST" action="{{ route('cart.add', $product) }}" id="addToCartForm" class="space-y-5">
                        @csrf
                        <input type="hidden" name="size" id="sizeInput" value="{{ $defaultSize ?? '' }}">

@if($sizes->count() === 1)
    <div>
        <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Ukuran</label>
        @php $single = $sizes->first(); @endphp
        <div class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3">
            <span class="text-sm font-bold tracking-wide text-gray-900">{{ $single->size }}</span>
            <span class="text-xs {{ $single->stock > 0 ? 'text-gray-500' : 'text-red-500 font-semibold' }}">
                {{ $single->stock > 0 ? $single->stock . ' tersedia' : 'Stok habis' }}
            </span>
        </div>
    </div>
@elseif($sizes->isNotEmpty())
    <div>
        <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Select Size <span class="text-red-500">*</span></label>
        <div class="flex flex-wrap gap-2">
            @foreach($sizes as $ps)
                <button type="button" data-size="{{ $ps->size }}"
                    class="size-chip px-4 py-2.5 rounded-lg border text-sm font-bold uppercase tracking-wider transition
                        {{ $ps->stock > 0 ? 'border-gray-300 hover:border-black' : 'border-gray-200 text-gray-300 line-through cursor-not-allowed' }}
                        {{ $defaultSize === $ps->size && $ps->stock > 0 ? 'bg-black text-white border-black' : ($ps->stock > 0 ? 'text-gray-700' : '') }}">
                    {{ $ps->size }}
                    <span class="block text-[9px] font-normal normal-case {{ $defaultSize === $ps->size && $ps->stock > 0 ? 'text-white/70' : 'text-gray-400' }}">{{ $ps->stock > 0 ? $ps->stock . ' left' : 'Out of stock' }}</span>
                </button>
            @endforeach
        </div>
    </div>
@else
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-700 leading-relaxed">
        Ukuran produk ini belum diatur oleh admin. Silakan cek kembali nanti.
    </div>
@endif

                        @if($hasAvailable)
                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-gray-200 rounded-full">
                                    <button type="button" id="qtyMinus" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-black transition font-label-sm">−</button>
                                    <input id="qty" name="qty" type="number" value="1" min="1" max="99"
                                        class="w-14 text-center bg-transparent outline-none text-sm font-bold py-2.5 [appearance:textfield]">
                                    <button type="button" id="qtyPlus" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-black transition font-label-sm">+</button>
                                </div>
                                <button type="submit" id="addToCartBtn"
                                    class="flex-grow px-6 py-4 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-base">shopping_bag</span> Add to Cart
                                </button>
                            </div>
                        @else
                            <div class="px-6 py-4 bg-gray-200 text-gray-500 font-bold uppercase text-xs tracking-widest rounded-full text-center">
                                Sold Out
                            </div>
                        @endif
                    </form>
                    <div class="mt-6 bg-[#f8f8f8] rounded-xl p-4 text-xs text-gray-500 leading-relaxed">
                        @auth
                            Produk akan masuk ke keranjangmu. Selesaikan pembayaran di halaman checkout.
                        @else
                            Kamu akan diminta login terlebih dahulu, lalu produk otomatis masuk ke keranjang.
                        @endauth
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-[#f8f8f8] rounded-2xl p-6 text-sm text-gray-600 max-w-md">
                <div class="flex items-center gap-3 py-1.5">
                    <span class="text-green-600 font-bold text-xs uppercase tracking-wider">100%</span>
                    <span class="text-gray-900 font-medium text-xs uppercase tracking-wide">Original Product</span>
                </div>
                <div class="flex items-center gap-3 py-1.5">
                    <span class="material-symbols-outlined text-lg text-green-600">credit_card</span>
                    <span class="text-gray-900 font-medium text-xs uppercase tracking-wide">Secure Payment</span>
                </div>
                <div class="flex items-center gap-3 py-1.5">
                    <span class="material-symbols-outlined text-lg text-green-600">local_shipping</span>
                    <span class="text-gray-900 font-medium text-xs uppercase tracking-wide">Fast Shipping</span>
                </div>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-24">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">You may also like</p>
                    <h2 class="font-headline-lg text-2xl font-extrabold uppercase tracking-tight">Related Products</h2>
                </div>
                <a href="{{ route('catalog.category', $product->category) }}" class="font-label-sm text-xs uppercase tracking-wider text-gray-500 hover:text-black transition-colors">
                    View All →
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($related as $item)
                    <a href="{{ route('product.show', $item) }}" class="group cursor-pointer">
                        <div class="relative aspect-square overflow-hidden bg-[#f8f8f8] mb-4 rounded-lg flex items-center justify-center">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-4/5 h-4/5 object-contain group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-4/5 h-4/5 flex items-center justify-center text-4xl text-gray-300">♡</div>
                            @endif
                        </div>
                        <h3 class="font-bold text-xs uppercase tracking-wider mb-1">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 font-label-sm">IDR {{ number_format((float)$item->price, 0, '.', '.') }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</section>

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
        const dotsWrap = document.getElementById('sliderDots');

        const dots = [];
        if (dotsWrap) {
            for (let i = 0; i < total; i++) {
                const dot = document.createElement('span');
                dot.className = 'w-1.5 h-1.5 rounded-full bg-white/50 transition';
                dot.addEventListener('click', function (e) { e.stopPropagation(); goTo(i); });
                dotsWrap.appendChild(dot);
                dots.push(dot);
            }
        }

        function goTo(i) {
            index = Math.max(0, Math.min(total - 1, i));
            track.style.transform = 'translateX(-' + (index * 100) + '%)';
            if (counter) counter.textContent = (index + 1) + '/' + total;
            if (prev) prev.disabled = index === 0;
            if (next) next.disabled = index === total - 1;
            dots.forEach(function (d, di) {
                d.className = 'w-1.5 h-1.5 rounded-full transition ' + (di === index ? 'bg-white w-4' : 'bg-white/50');
            });
        }

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

    (function () {
        const input = document.getElementById('qty');
        const minus = document.getElementById('qtyMinus');
        const plus = document.getElementById('qtyPlus');
        if (!input) return;
        const clamp = function (v) { return Math.max(1, Math.min(99, v)); };
        if (minus) minus.addEventListener('click', function () { input.value = clamp((parseInt(input.value, 10) || 1) - 1); });
        if (plus) plus.addEventListener('click', function () { input.value = clamp((parseInt(input.value, 10) || 1) + 1); });
        input.addEventListener('change', function () { input.value = clamp(parseInt(input.value, 10) || 1); });
    })();

    (function () {
        const chips = document.querySelectorAll('.size-chip');
        const input = document.getElementById('sizeInput');
        if (!chips.length || !input) return;

        const setActive = function (el) {
            chips.forEach(function (c) {
                if (c.classList.contains('cursor-not-allowed')) return;
                c.classList.remove('bg-black', 'text-white', 'border-black');
                c.classList.add('border-gray-300', 'text-gray-700');
            });
            el.classList.add('bg-black', 'text-white', 'border-black');
            el.classList.remove('border-gray-300', 'text-gray-700');
            input.value = el.dataset.size;
        };

        chips.forEach(function (c) {
            c.addEventListener('click', function () {
                if (c.classList.contains('cursor-not-allowed')) return;
                setActive(c);
            });
        });

        if (input.value) {
            const match = Array.from(chips).find(function (c) {
                return c.dataset.size === input.value && !c.classList.contains('cursor-not-allowed');
            });
            if (match) setActive(match);
        }
    })();
</script>
@endsection