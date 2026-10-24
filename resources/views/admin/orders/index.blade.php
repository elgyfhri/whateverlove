@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Orders</h1>
        <p class="mt-1 text-sm text-gray-500">Daftar semua pesanan & status pengirimannya.</p>
    </div>
</div>

<form method="GET" class="mb-6 flex gap-3">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari kode pesanan, nama, atau nomor HP" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-black focus:outline-none">
    <button class="px-5 py-3 bg-black text-white rounded-xl text-xs font-bold uppercase tracking-widest">Cari</button>
</form>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 overflow-x-auto">
    <table class="w-full text-left text-sm min-w-[820px]">
        <thead>
            <tr class="text-[10px] uppercase tracking-widest text-gray-400 border-b border-gray-100">
                <th class="px-6 py-4">Invoice</th>
                <th class="px-6 py-4">Pelanggan</th>
                <th class="px-6 py-4">Tanggal</th>
                <th class="px-6 py-4">Total</th>
                <th class="px-6 py-4">Pembayaran</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Resi</th>
                <th class="px-6 py-4"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr class="border-b border-gray-100 hover:bg-gray-50/60 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}" class="font-bold tracking-wider hover:underline">{{ $order->invoice_no }}</a>
                        <p class="text-xs text-gray-400 font-label-sm mt-0.5">{{ $order->items->count() }} item</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-xs uppercase tracking-wider">{{ $order->name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 font-bold font-label-sm">IDR {{ number_format($order->total, 0, '.', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="font-label-sm text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-full @if($order->payment_status === 'paid') bg-green-50 text-green-600 @elseif($order->payment_status === 'failed') bg-red-50 text-red-600 @else bg-amber-50 text-amber-600 @endif">
                            {{ $order->payment_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-label-sm text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-full @if($order->status === 'delivered') bg-green-50 text-green-600 @elseif($order->status === 'shipped') bg-blue-50 text-blue-600 @elseif($order->status === 'processing') bg-amber-50 text-amber-600 @else bg-gray-100 text-gray-600 @endif">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs font-label-sm {{ $order->tracking_number ? 'text-black' : 'text-gray-300' }}">{{ $order->tracking_number ?? '—' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex px-4 py-2 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-300 hover:border-black hover:text-black transition">Kelola</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-14 text-center text-gray-400 text-sm">Belum ada pesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-8">
    {{ $orders->links() }}
</div>
@endsection