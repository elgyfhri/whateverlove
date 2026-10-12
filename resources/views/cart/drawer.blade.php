@if($drawerCart && $drawerCart->items->isNotEmpty())
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">
        @foreach($drawerCart->items as $item)
            <div class="flex gap-4" data-cart-item="{{ $item->id }}"
                data-update="{{ route('cart.update', $item) }}"
                data-remove="{{ route('cart.remove', $item) }}">
                <a href="{{ route('product.show', $item->product) }}" class="shrink-0 w-16 h-16 bg-[#f8f8f8] rounded-lg overflow-hidden flex items-center justify-center">
                    @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xl text-gray-300">♡</span>
                    @endif
                </a>
                <div class="flex-grow min-w-0">
                    <a href="{{ route('product.show', $item->product) }}" class="block font-bold text-[10px] uppercase tracking-wider hover:underline truncate">{{ $item->product->name }}</a>
                    @if($item->size)
                        <p class="mt-0.5 text-[10px] font-label-sm text-gray-400">Size <span class="font-bold text-gray-600">{{ $item->size }}</span></p>
                    @endif
                    <p class="mt-0.5 text-[10px] text-gray-500 font-label-sm">IDR {{ number_format((float)$item->product->price, 0, '.', '.') }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <button type="button" data-qty-dec="{{ $item->qty }}" aria-label="Kurangi" class="w-6 h-6 flex items-center justify-center border border-gray-200 rounded-full text-gray-600 hover:border-black transition text-xs">−</button>
                        <span class="w-6 text-center text-xs font-bold">{{ $item->qty }}</span>
                        <button type="button" data-qty-inc="{{ $item->qty }}" aria-label="Tambah" class="w-6 h-6 flex items-center justify-center border border-gray-200 rounded-full text-gray-600 hover:border-black transition text-xs">+</button>
                    </div>
                </div>
                <p class="font-label-sm text-xs font-bold w-16 text-right whitespace-nowrap">IDR {{ number_format($item->total, 0, '.', '.') }}</p>
                <button type="button" data-cart-remove aria-label="Hapus" class="material-symbols-outlined text-base text-gray-400 hover:text-red-500 transition self-start">close</button>
            </div>
        @endforeach
    </div>
    <div class="border-t border-gray-100 px-5 py-5 space-y-3">
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Subtotal</span>
            <span class="font-bold">IDR {{ number_format($drawerCart->subtotal, 0, '.', '.') }}</span>
        </div>
        <a href="{{ route('checkout') }}" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
            Checkout <span class="material-symbols-outlined text-sm">arrow_outward</span>
        </a>
        <a href="{{ route('cart.index') }}" class="w-full flex items-center justify-center px-6 py-3 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-200 hover:border-black transition">
            View Full Cart
        </a>
    </div>
@else
    <div class="flex-1 flex flex-col items-center justify-center px-8 text-center">
        <div class="text-4xl mb-3">🛍️</div>
        <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight">Keranjang kosong</h3>
        <p class="mt-2 text-gray-500 text-sm">Jelajahi katalog dan tambahkan favoritmu.</p>
        <a href="{{ route('catalog') }}" class="mt-6 px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">Start Shopping</a>
        @guest
            <a href="{{ route('login') }}" class="mt-3 text-gray-500 font-label-sm text-xs uppercase tracking-widest hover:text-black transition">Login untuk lihat keranjangmu →</a>
        @endguest
    </div>
@endif