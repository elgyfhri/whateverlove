@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<section class="py-16 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-xs font-label-sm uppercase tracking-widest text-gray-500 hover:text-black transition-colors mb-8">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Pesanan Saya
        </a>

        @if(session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        @php
            $tracking = [];
            $tracking[] = ['label' => 'Pesanan Dibuat', 'sub' => $order->created_at->format('d M Y H:i'), 'status' => 'done'];
            if ($order->payment_status === 'paid') {
                $tracking[] = ['label' => 'Pembayaran Dikonfirmasi', 'sub' => $order->paid_at?->format('d M Y H:i') ?? 'Lunas', 'status' => 'done'];
            } elseif ($order->payment_status === 'pending') {
                $tracking[] = ['label' => 'Pembayaran Dikonfirmasi', 'sub' => 'Menunggu pembayaran', 'status' => 'current'];
            } else {
                $tracking[] = ['label' => 'Pembayaran Dikonfirmasi', 'sub' => ucfirst($order->payment_status), 'status' => 'todo'];
            }
            $p = in_array($order->status, ['shipped', 'delivered']);
            $tracking[] = ['label' => 'Diproses / Dikemas', 'sub' => $p ? 'Selesai diproses' : ($order->status === 'processing' ? 'Toko sedang mengemas pesananmu' : ($order->payment_status === 'paid' ? 'Menunggu diproses' : '')), 'status' => $p ? 'done' : ($order->status === 'processing' ? 'current' : 'todo')];
            if (in_array($order->status, ['shipped', 'delivered'])) {
                $tracking[] = ['label' => 'Dikirim', 'sub' => 'No. resi: ' . ($order->tracking_number ?? '—'), 'status' => $order->status === 'delivered' ? 'done' : 'current'];
            } else {
                $tracking[] = ['label' => 'Dikirim', 'sub' => '', 'status' => 'todo'];
            }
            $tracking[] = ['label' => 'Pesanan Diterima', 'sub' => $order->status === 'delivered' ? ($order->delivered_at?->format('d M Y H:i') ?? 'Sampai') : '', 'status' => $order->status === 'delivered' ? 'done' : 'todo'];
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Lacak Pesanan</p>
                @if($order->status === 'shipped')
                    <span class="font-label-sm text-[10px] uppercase tracking-widest bg-blue-50 text-blue-600 px-3 py-1 rounded-full">Paket dalam perjalanan</span>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                @foreach($tracking as $step)
                    <div class="flex-1 relative">
                        <div class="flex items-start gap-3 sm:flex-col sm:gap-2">
                            <div class="flex items-center">
                                <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center @if($step['status'] === 'done') bg-green-100 text-green-600 @elseif($step['status'] === 'current') bg-black text-white @else bg-gray-100 text-gray-300 @endif">
                                    @if($step['status'] === 'done')
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    @elseif($step['status'] === 'current')
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                    @else
                                        <span class="material-symbols-outlined text-sm">more_horiz</span>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <div class="ml-3 sm:hidden flex-1 h-px bg-gray-100"></div>
                                @endif
                            </div>
                            <div class="min-w-0 pb-4 sm:pt-2">
                                <p class="font-bold text-xs uppercase tracking-wider {{ $step['status'] === 'todo' ? 'text-gray-400' : 'text-black' }}">{{ $step['label'] }}</p>
                                @if($step['sub'])
                                    <p class="text-xs text-gray-500 mt-0.5 font-label-sm">{{ $step['sub'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="hidden sm:flex mb-1 relative">
                @foreach($tracking as $i => $step)
                    @if(!$loop->last)
                        <div class="flex-1">
                            <div class="ml-4 mr-2 h-px mt-3 @if(in_array($tracking[$i + 1]['status'], ['done', 'current']) || $step['status'] === 'done') bg-green-400 @else bg-gray-100 @endif"></div>
                        </div>
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-4 font-label-sm">
                 Estimasi pengiriman: {{ $order->shippingEtaLabel() }} · {{ $order->shipping_courier ? strtoupper($order->shipping_courier) . ' ' . $order->shipping_service : '—' }}
             </p>
             @if($order->status === 'shipped' && $order->tracking_number)
                 <div class="mt-5 flex flex-col sm:flex-row gap-3">
                     <a href="https://www.jne.co.id/tracking-package" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-black text-white rounded-full font-bold uppercase text-[10px] tracking-widest">Cek Tracking JNE <span class="material-symbols-outlined text-sm">open_in_new</span></a>
                     <form method="POST" action="{{ route('orders.received', $order) }}" class="flex-1 confirm-received-form">@csrf @method('PATCH')<button type="submit" class="w-full px-5 py-3 border border-gray-300 rounded-full font-bold uppercase text-[10px] tracking-widest hover:border-black">Pesanan Sudah Diterima</button></form>
                 </div>
             @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Invoice</p>
                    <p class="font-bold text-sm tracking-wider">{{ $order->invoice_no }}</p>
                    <p class="text-xs text-gray-400 font-label-sm mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <span class="font-label-sm text-[10px] uppercase tracking-widest px-3 py-1 rounded-full @if($order->payment_status === 'paid') bg-green-50 text-green-600 @elseif($order->payment_status === 'failed') bg-red-50 text-red-600 @elseif($order->payment_status === 'expired' || $order->payment_status === 'cancelled') bg-gray-100 text-gray-500 @else bg-amber-50 text-amber-600 @endif">
                    @if($order->payment_status === 'paid') Pembayaran Dikonfirmasi
                    @elseif($order->payment_status === 'pending') Menunggu Pembayaran
                    @elseif($order->payment_status === 'failed') Pembayaran Gagal
                    @elseif($order->payment_status === 'expired') Kedaluwarsa
                    @elseif($order->payment_status === 'cancelled') Dibatalkan
                    @else {{ $order->payment_status }} @endif
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
                            Pembayaran sudah dikonfirmasi (IDR {{ number_format($order->total, 0, '.', '.') }}).
                            Pesanan akan segera kami proses; nomor resi dikirim setelah di-packing.
                        </p>
                    </div>
                @elseif($order->payment_status === 'pending' && $order->payment_redirect_url)
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Pesanan ini belum dibayar.
                        @if($order->paymentTypeLabel())
                            Metode: <span class="font-bold text-black">{{ $order->paymentTypeLabel() }}</span>.
                        @endif
                        Klik tombol di bawah untuk melanjutkan pembayaran
                        (mode {{ config('midtrans.is_production') ? 'produksi' : 'sandbox' }} Midtrans), atau ganti metode bila perlu.
                    </p>

                    @if($instruction = $order->paymentInstruction())
                        @if($instruction['number'])
                            <div class="mt-5 bg-white border border-amber-200 rounded-xl p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-amber-600 text-lg">account_balance_wallet</span>
                                    <p class="font-bold text-xs uppercase tracking-widest text-amber-700">{{ $instruction['label'] }}</p>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">Nomor yang perlu Anda transfer atau tunjukkan di gerai:</p>
                                <p class="font-mono text-lg font-bold tracking-widest select-all break-all">{{ $instruction['number'] }}</p>
                                <p class="text-[11px] text-gray-400 mt-3 leading-relaxed">
                                    Selesaikan pembayaran sebelum batas waktu agar pesanan tidak kedaluwarsa.
                                </p>
                            </div>
                        @endif
                    @endif

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ $order->payment_redirect_url }}" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
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
                        Ini belum diselesaikan ({{ $order->payment_status }}). Kalau butuh bantuan,
                        hubungi kami lewat halaman
                        <a href="{{ route('contact') }}" class="font-bold text-black underline underline-offset-2">Contact Us</a>.
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        @if($order->payment_status === 'pending' && $order->payment_redirect_url)
                            <a href="{{ route('payment.return', ['order' => $order]) }}" onclick="event.preventDefault(); window.open('{{ $order->payment_redirect_url }}', '_blank', 'noopener');" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                                Bayar Sekarang <span class="material-symbols-outlined text-sm">arrow_outward</span>
                            </a>
                        @endif
                        <a href="{{ route('catalog') }}" class="@if($order->payment_status === 'pending') flex-1 @else flex-1 @endif inline-flex items-center justify-center px-6 py-3.5 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black transition">
                            Lanjut Belanja
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.confirm-received-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        Swal.fire({
            title: 'Pesanan sudah diterima?',
            text: 'Status pesanan akan diubah menjadi Diterima.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#111111',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, sudah diterima',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection