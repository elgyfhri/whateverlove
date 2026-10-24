@extends('layouts.admin')

@section('title', 'Order ' . $order->invoice_no)

@section('content')
<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-xs font-label-sm uppercase tracking-widest text-gray-500 hover:text-black transition-colors mb-8">
    <span class="material-symbols-outlined text-sm">arrow_back</span> Orders
</a>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Invoice</p>
                    <p class="font-bold text-sm tracking-wider">{{ $order->invoice_no }}</p>
                    <p class="text-xs text-gray-400 font-label-sm mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="inline-flex items-center gap-1.5 font-label-sm text-[10px] uppercase tracking-widest px-4 py-2 rounded-full border border-gray-300 text-gray-600 hover:border-black hover:text-black transition mb-3">
                        <span class="material-symbols-outlined text-sm">print</span> Cetak
                    </a>
                    <div>
                        <span class="font-label-sm text-[10px] uppercase tracking-widest px-3 py-1 rounded-full @if($order->payment_status === 'paid') bg-green-50 text-green-600 @else bg-amber-50 text-amber-600 @endif">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 border-b border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-2">Penerima</p>
                    <p class="font-bold text-sm">{{ $order->name }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $order->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $order->email }}</p>
                </div>
                <div>
                    <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-2">Alamat</p>
                    <p class="text-sm text-gray-700">{{ $order->address }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $order->district_name }}, {{ $order->city_name }}, {{ $order->province_name }}</p>
                    @if($order->postal_code)<p class="text-xs text-gray-400">Kode Pos {{ $order->postal_code }}</p>@endif
                </div>
            </div>

            <div class="px-8 py-6 border-b border-gray-100">
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-4">Items</p>
                <ul class="space-y-3">
                    @foreach($order->items as $item)
                        <li class="flex items-center gap-4">
                            <div class="shrink-0 w-12 h-12 bg-[#f8f8f8] rounded-lg overflow-hidden">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="flex items-center justify-center h-full text-gray-300">♡</span>
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

            <div class="px-8 py-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-bold">IDR {{ number_format($order->subtotal, 0, '.', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm mt-2">
                    <span class="text-gray-500">Ongkir</span>
                    <span class="font-bold">IDR {{ number_format($order->shipping_cost, 0, '.', '.') }}</span>
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
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-4">Update Status Pengiriman</p>

            @if($order->payment_status !== 'paid')
                <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4">
                    Pesanan belum dibayar — status pengiriman baru dapat diubah setelah pembayaran dikonfirmasi.
                </p>
            @endif

            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PATCH')

                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Status Pembayaran</label>
                <select name="payment_status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-black focus:outline-none">
                    @foreach(['pending' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'failed' => 'Gagal', 'cancelled' => 'Dibatalkan'] as $val => $label)
                        <option value="{{ $val }}" @if($order->payment_status === $val) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>

                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2 mt-5">Status Pesanan</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white focus:border-black focus:outline-none">
                    @foreach(['pending' => 'Pending', 'processing' => 'Diproses / Dikemas', 'shipped' => 'Dikirim', 'delivered' => 'Diterima', 'cancelled' => 'Dibatalkan'] as $val => $label)
                        <option value="{{ $val }}" @if($order->status === $val) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>

                <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2 mt-5">No. Resi / Tracking</label>
                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" maxlength="64"
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-black focus:outline-none"
                    placeholder="mis. JNE0090012345001">

                 @if($order->tracking_number)
                     <a href="https://www.jne.co.id/tracking-package" target="_blank" rel="noopener" class="mt-3 w-full inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-700 font-bold uppercase text-[10px] tracking-widest py-3 rounded-lg hover:border-black">Cek Tracking JNE <span class="material-symbols-outlined text-sm">open_in_new</span></a>
                 @endif

                 <button type="submit" class="mt-6 w-full bg-black text-white font-bold uppercase text-xs tracking-widest py-3.5 rounded-lg hover:opacity-80 transition">
                    Simpan
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-4">Riwayat</p>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex justify-between"><span>Dibuat</span><span class="font-bold">{{ $order->created_at->format('d M Y H:i') }}</span></li>
                <li class="flex justify-between"><span>Dibayar</span><span class="font-bold">{{ $order->paid_at?->format('d M Y H:i') ?? '—' }}</span></li>
                <li class="flex justify-between"><span>Dikirim</span><span class="font-bold">{{ $order->shipped_at?->format('d M Y H:i') ?? '—' }}</span></li>
                <li class="flex justify-between"><span>Diterima</span><span class="font-bold">{{ $order->delivered_at?->format('d M Y H:i') ?? '—' }}</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection