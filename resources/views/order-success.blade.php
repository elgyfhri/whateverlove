@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@php
    $instruction = $order->paymentInstruction();
    $paymentType = $order->paymentTypeLabel();
@endphp

@section('content')
<section class="py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="max-w-2xl mx-auto text-center mb-14">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-green-50 flex items-center justify-center text-green-600">
            <span class="material-symbols-outlined text-3xl">check</span>
        </div>
        <h1 class="font-headline-lg text-4xl md:text-5xl font-extrabold uppercase tracking-tight">Pesanan Diterima</h1>
        <p class="mt-4 text-gray-500 text-sm">Terima kasih! Pesananmu sudah dicatat. Detail lengkap ada di bawah.</p>
    </div>

    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Invoice</p>
                <p class="font-bold text-sm tracking-wider">{{ $order->invoice_no }}</p>
            </div>
            <span class="font-label-sm text-[10px] uppercase tracking-widest px-3 py-1 rounded-full @if($order->payment_status === 'paid') bg-green-50 text-green-600 @else bg-amber-50 text-amber-600 @endif">
                @if($order->payment_status === 'paid') Pembayaran Dikonfirmasi @elseif($order->payment_status === 'expired') Pembayaran Kedaluwarsa @elseif($order->payment_status === 'failed') Pembayaran Gagal @else Menunggu Pembayaran @endif
            </span>
        </div>

        <div class="px-8 py-6 border-b border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-2">Penerima</p>
                <p class="font-bold text-sm">{{ $order->name }}</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ $order->phone }}</p>
                <p class="text-sm text-gray-500">{{ $order->email }}</p>
            </div>
            <div>
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-2">Alamat Pengiriman</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $order->address }}</p>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $order->district_name }}, {{ $order->city_name }}, {{ $order->province_name }}
                </p>
                @if($order->postal_code)<p class="text-sm text-gray-500">Kode Pos {{ $order->postal_code }}</p>@endif
            </div>
        </div>

        <div class="px-8 py-6 border-b border-gray-100">
            <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-4">Items</p>
            <ul class="space-y-3">
                @foreach($order->items as $item)
                    <li class="flex items-center gap-4">
                        <div class="shrink-0 w-12 h-12 bg-[#f8f8f8] rounded-lg overflow-hidden flex items-center justify-center">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-300">♡</span>
                            @endif
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="font-bold text-xs uppercase tracking-wider truncate">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-400 font-label-sm mt-0.5">
                                @if($item->size)Size {{ $item->size }} · @endif Qty {{ $item->qty }}
                            </p>
                        </div>
                        <p class="font-label-sm text-sm font-bold">IDR {{ number_format($item->price * $item->qty, 0, '.', '.') }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="px-8 py-6 border-b border-gray-100">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-bold">IDR {{ number_format($order->subtotal, 0, '.', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm mt-2">
                <span class="text-gray-500">Ongkir</span>
                <span class="font-bold">
                    @if($order->shipping_cost)
                        IDR {{ number_format($order->shipping_cost, 0, '.', '.') }}
                    @else
                        Dihitung kemudian
                    @endif
                </span>
            </div>
            @if($order->shipping_courier)
                <div class="flex justify-between text-xs mt-2 text-gray-500">
                    <span>Kurir</span>
                    <span class="font-bold">{{ strtoupper($order->shipping_courier) }} {{ $order->shipping_service }}</span>
                </div>
                @if($order->shipping_estimate)
                    <div class="flex justify-between text-xs mt-1 text-gray-500">
                        <span>Estimasi</span>
                        <span class="font-bold">{{ $order->shippingEtaLabel() }}</span>
                    </div>
                @endif
            @endif
            <div class="flex justify-between text-sm mt-4 pt-4 border-t border-gray-100">
                <span class="font-label-sm uppercase tracking-widest text-xs text-gray-400">Total</span>
                <span class="font-headline-lg text-lg font-extrabold">IDR {{ number_format($order->total, 0, '.', '.') }}</span>
            </div>
        </div>

        <div class="px-8 py-8 bg-[#f8f8f8]">
            @if($order->payment_status === 'paid')
                <div class="flex items-start gap-3 text-sm text-green-700">
                    <span class="material-symbols-outlined text-green-600">verified</span>
                    <p class="leading-relaxed">
                        Pembayaran untuk invoice <span class="font-bold text-black">{{ $order->invoice_no }}</span>
                        sudah dikonfirmasi (IDR {{ number_format($order->total, 0, '.', '.') }}).
                        Pesanan akan segera kami proses; nomor resi dikirim setelah di-packing.
                    </p>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('orders.show', $order) }}" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                        Lihat Pesanan Saya
                    </a>
                    <a href="{{ route('catalog') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black transition">
                        Continue Shopping
                    </a>
                </div>
            @elseif($order->payment_redirect_url)
                <p class="text-xs text-gray-600 leading-relaxed">
                    Pembayaran untuk invoice <span class="font-bold text-black">{{ $order->invoice_no }}</span>
                    belum diselesaikan.
                    @if($paymentType)
                        Metode: <span class="font-bold text-black">{{ $paymentType }}</span>.
                    @endif
                    Klik tombol di bawah untuk melanjutkan pembayaran di halaman aman
                    Midtrans (mode <span class="font-bold text-black">{{ config('midtrans.is_production') ? 'produksi' : 'sandbox' }}</span>). Setelah membayar,
                    status order otomatis diperbarui.
                </p>

                @if($instruction && $instruction['number'])
                    <div class="mt-5 bg-white border border-amber-200 rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-amber-600 text-lg">account_balance_wallet</span>
                            <p class="font-bold text-xs uppercase tracking-widest text-amber-700">{{ $instruction['label'] }}</p>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Nomor yang perlu Anda transfer atau tunjukkan di gerai:</p>
                        <p class="font-mono text-lg font-bold tracking-widest select-all break-all">{{ $instruction['number'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-3 leading-relaxed">
                            Selesaikan pembayaran sebelum batas waktu agar pesanan tidak kedaluwarsa.
                            Setelah transfer, status akan otomatis diperbarui — atau klik "Cek Status".
                        </p>
                    </div>
                @endif

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ $order->payment_redirect_url }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition" target="_blank" rel="noopener">
                        Bayar Sekarang <span class="material-symbols-outlined text-sm">arrow_outward</span>
                    </a>
                    <a href="{{ route('payment.status', $order) }}" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black transition">
                        Cek Status
                    </a>
                    <a href="{{ route('payment.rechoose', $order) }}" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black transition">
                        Ganti Metode Bayar
                    </a>
                </div>
            @else
                <p class="text-xs text-gray-600 leading-relaxed">
                    Pembayaran saat ini dilakukan melalui marketplace resmi kami. Kirim nomor invoice
                    <span class="font-bold text-black">{{ $order->invoice_no }}</span>
                    lewat halaman
                    <a href="{{ route('contact') }}" class="font-bold text-black underline underline-offset-2">Contact Us</a>
                    untuk menyelesaikan pembayaran. Nomor resi akan dikirim setelah pembayaran dikonfirmasi.
                </p>
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('contact') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                        Contact Us <span class="material-symbols-outlined text-sm">arrow_outward</span>
                    </a>
                    <a href="{{ route('catalog') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black transition">
                        Continue Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection