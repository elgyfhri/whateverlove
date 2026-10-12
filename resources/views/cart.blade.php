@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<section class="py-16 md:py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
        <div>
            <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Shopping bag</p>
            <h1 class="font-headline-lg text-4xl md:text-6xl font-extrabold uppercase tracking-tight">Cart</h1>
            <p class="mt-3 text-sm text-gray-500">{{ $cart->items_count }} item{{ $cart->items_count !== 1 ? 's' : '' }}</p>
        </div>
        <a href="{{ route('catalog') }}" class="font-label-sm text-xs uppercase tracking-widest text-gray-500 hover:text-black transition-colors">Continue Shopping →</a>
    </div>

    @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    @if($cart->items->isEmpty())
        <div class="text-center py-24 bg-[#f8f8f8] rounded-2xl">
            <div class="text-5xl mb-4">🛍️</div>
            <h2 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">Your cart is empty</h2>
            <p class="mt-2 text-gray-500 text-sm">Jelajahi katalog dan tambahkan produk favorit kamu.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-8 px-8 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                Start Shopping
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 flex gap-5 items-center">
                        <a href="{{ route('product.show', $item->product) }}" class="shrink-0 w-24 h-24 bg-[#f8f8f8] rounded-xl overflow-hidden flex items-center justify-center">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl text-gray-300">♡</span>
                            @endif
                        </a>

                        <div class="flex-grow min-w-0">
                            <a href="{{ route('product.show', $item->product) }}" class="font-bold text-xs uppercase tracking-wider hover:underline">{{ $item->product->name }}</a>
                            @if($item->size)
                                <p class="mt-1 text-xs font-label-sm text-gray-400">Size: <span class="font-bold text-gray-600">{{ $item->size }}</span></p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500 font-label-sm">IDR {{ number_format((float)$item->product->price, 0, '.', '.') }}</p>
                        </div>

                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <button type="button" class="qty-minus w-8 h-8 flex items-center justify-center border border-gray-200 rounded-full text-gray-600 hover:border-black transition font-label-sm">−</button>
                            <input type="number" name="qty" value="{{ $item->qty }}" min="1" max="99"
                                class="qty-input w-14 text-center border-b-2 border-gray-300 focus:border-black outline-none py-1 text-sm">
                            <button type="button" class="qty-plus w-8 h-8 flex items-center justify-center border border-gray-200 rounded-full text-gray-600 hover:border-black transition font-label-sm">+</button>
                            <button type="submit" class="hidden" aria-hidden="true">save</button>
                        </form>

                        <p class="font-label-sm text-sm font-bold w-24 text-right">IDR {{ number_format($item->total, 0, '.', '.') }}</p>

                        <form method="POST" action="{{ route('cart.remove', $item) }}" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button class="material-symbols-outlined text-lg text-gray-400 hover:text-red-500 transition" aria-label="Remove">close</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-8 lg:sticky lg:top-32">
                <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-6">Order Summary</h2>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-bold">IDR {{ number_format($cart->subtotal, 0, '.', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Shipping</span>
                        <span class="text-gray-400">Dihitung saat checkout</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-100 pt-4">
                        <span class="font-label-sm uppercase tracking-widest text-xs text-gray-400">Total</span>
                        <span class="font-headline-lg text-xl font-extrabold">IDR {{ number_format($cart->subtotal, 0, '.', '.') }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout') }}"
                    class="mt-8 w-full px-6 py-4 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition flex items-center justify-center gap-2">
                    Proceed to Checkout <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
                <a href="{{ route('contact') }}" class="mt-3 w-full px-6 py-4 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-200 hover:border-black transition flex items-center justify-center">
                    Butuh bantuan?
                </a>
            </div>
        </div>
    @endif
</section>

<script>
    document.querySelectorAll('.qty-minus, .qty-plus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = btn.closest('form');
            const input = form.querySelector('.qty-input');
            if (!input) return;
            const isPlus = btn.classList.contains('qty-plus');
            input.value = Math.max(1, Math.min(99, (parseInt(input.value, 10) || 1) + (isPlus ? 1 : -1)));
            form.requestSubmit();
        });
    });
</script>
@endsection