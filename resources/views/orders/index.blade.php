@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<section class="py-16 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="max-w-4xl mx-auto">
        <div class="mb-10">
            <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Account</p>
            <h1 class="font-headline-lg text-4xl md:text-5xl font-extrabold uppercase tracking-tight">Pesanan Saya</h1>
            <p class="mt-3 text-gray-500 text-sm">Riwayat pembelian dan status pembayaranmu.</p>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @if($orders->isEmpty())
            <div class="text-center py-20 border border-dashed border-gray-200 rounded-2xl">
                <span class="material-symbols-outlined text-4xl text-gray-300">receipt_long</span>
                <p class="mt-4 font-bold text-sm uppercase tracking-wider">Belum ada pesanan</p>
                <p class="mt-2 text-sm text-gray-500">Yuk mulai belanja di katalog.</p>
                <a href="{{ route('catalog') }}" class="mt-6 inline-flex items-center justify-center px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Ke Katalog
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach($orders as $order)
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div>
                                    <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Invoice</p>
                                    <a href="{{ route('orders.show', $order) }}" class="font-bold text-sm tracking-wider hover:underline">{{ $order->invoice_no }}</a>
                                </div>
                                <span class="text-xs text-gray-400 font-label-sm">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-label-sm text-[10px] uppercase tracking-widest px-3 py-1 rounded-full @if($order->payment_status === 'paid') bg-green-50 text-green-600 @elseif($order->payment_status === 'failed') bg-red-50 text-red-600 @elseif($order->payment_status === 'expired' || $order->payment_status === 'cancelled') bg-gray-100 text-gray-500 @else bg-amber-50 text-amber-600 @endif">
                                    @if($order->payment_status === 'paid') Dibayar
                                    @elseif($order->payment_status === 'pending') Menunggu Pembayaran
                                    @elseif($order->payment_status === 'failed') Pembayaran Gagal
                                    @elseif($order->payment_status === 'expired') Kedaluwarsa
                                    @elseif($order->payment_status === 'cancelled') Dibatalkan
                                    @else {{ $order->payment_status }} @endif
                                </span>
                                @if($order->payment_status === 'paid' || $order->status !== 'pending')
                                    <span class="font-label-sm text-[10px] uppercase tracking-widest px-3 py-1 rounded-full @if($order->status === 'processing') bg-blue-50 text-blue-600 @elseif($order->status === 'shipped') bg-indigo-50 text-indigo-600 @elseif($order->status === 'delivered') bg-green-50 text-green-600 @elseif($order->status === 'cancelled') bg-gray-100 text-gray-500 @else bg-amber-50 text-amber-600 @endif">
                                        {{ $order->statusLabel() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                @php $first = $order->items->first(); @endphp
                                <div class="shrink-0 w-12 h-12 bg-[#f8f8f8] rounded-lg overflow-hidden flex items-center justify-center">
                                    @if($first && $first->image)
                                        <img src="{{ asset('storage/' . $first->image) }}" alt="{{ $first->product_name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-gray-300">♡</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-xs uppercase tracking-wider truncate">
                                        {{ $first ? $first->product_name : '—' }}
                                        @if($order->items->count() > 1)
                                            <span class="text-gray-400">+{{ $order->items->count() - 1 }} lainnya</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 font-label-sm mt-0.5">{{ $order->items->sum('qty') }} item · {{ $order->shipping_courier ? strtoupper($order->shipping_courier) . ' ' . $order->shipping_service : 'Ongkir dihitung' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Total</p>
                                    <p class="font-headline-lg font-extrabold">IDR {{ number_format($order->total, 0, '.', '.') }}</p>
                                </div>
                                @if($order->payment_status === 'pending' && $order->status === 'pending' && $order->payment_redirect_url)
                                    <a href="{{ route('payment.return', ['order' => $order]) }}" onclick="event.preventDefault(); window.open('{{ $order->payment_redirect_url }}', '_blank', 'noopener');" class="px-5 py-2.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">Bayar</a>
                                @endif
                                <a href="{{ route('orders.show', $order) }}" class="px-5 py-2.5 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black transition">Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</section>
@endsection