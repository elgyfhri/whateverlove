@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Welcome back</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="self-start md:self-auto inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
        <span class="material-symbols-outlined text-sm">add</span> Add Product
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-gray-300 transition">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400">Orders</p>
        <p class="mt-3 text-4xl font-extrabold font-label-sm">{{ $orderStats['total'] }}</p>
        <div class="mt-4 grid grid-cols-3 gap-2">
            <div class="rounded-xl bg-amber-50 px-3 py-2.5">
                <p class="text-lg font-extrabold font-label-sm text-amber-600">{{ $orderStats['pending'] }}</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 mt-0.5">Menunggu Bayar</p>
            </div>
            <div class="rounded-xl bg-blue-50 px-3 py-2.5">
                <p class="text-lg font-extrabold font-label-sm text-blue-600">{{ $orderStats['processing'] + $orderStats['shipped'] }}</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 mt-0.5">Proses / Kirim</p>
            </div>
            <div class="rounded-xl bg-green-50 px-3 py-2.5">
                <p class="text-lg font-extrabold font-label-sm text-green-600">{{ $orderStats['completed'] }}</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 mt-0.5">Selesai</p>
            </div>
        </div>
        <div class="mt-4 w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-lg">receipt_long</span>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <a href="{{ route('admin.products.index') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-gray-300 transition">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400">Products</p>
        <p class="mt-3 text-4xl font-extrabold font-label-sm">{{ \App\Models\Product::count() }}</p>
        <p class="mt-2 text-xs font-label-sm text-gray-400">Aktif: <span class="text-green-600 font-bold">{{ \App\Models\Product::where('is_active', true)->count() }}</span></p>
        <div class="mt-4 w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-lg">inventory_2</span>
        </div>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-gray-300 transition">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400">Categories</p>
        <p class="mt-3 text-4xl font-extrabold font-label-sm">{{ \App\Models\Category::count() }}</p>
        <div class="mt-4 w-10 h-10 rounded-xl bg-secondary text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-lg">category</span>
        </div>
    </a>
    <a href="{{ route('admin.articles.index') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-gray-300 transition">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400">Articles</p>
        <p class="mt-3 text-4xl font-extrabold font-label-sm">{{ \App\Models\Article::count() }}</p>
        <div class="mt-4 w-10 h-10 rounded-xl bg-[#111111] text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-lg">menu_book</span>
        </div>
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-10">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400">Pesanan Terbaru</h2>
            <p class="text-sm font-bold mt-1">Pantau & update status pengiriman</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-label-sm uppercase tracking-widest text-gray-500 hover:text-black transition-colors">Lihat Semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm min-w-[720px]">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-gray-400 border-b border-gray-100">
                    <th class="px-6 py-3">Invoice</th>
                    <th class="px-6 py-3">Pelanggan</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Pembayaran</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr class="border-b border-gray-100 hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3 font-bold tracking-wider">{{ $order->invoice_no }}</td>
                        <td class="px-6 py-3 text-xs">{{ $order->name }}</td>
                        <td class="px-6 py-3 font-label-sm font-bold">IDR {{ number_format($order->total, 0, '.', '.') }}</td>
                        <td class="px-6 py-3">
                            <span class="font-label-sm text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-full @if($order->payment_status === 'paid') bg-green-50 text-green-600 @else bg-amber-50 text-amber-600 @endif">{{ $order->payment_status }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-label-sm text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-full @if($order->status === 'delivered') bg-green-50 text-green-600 @elseif($order->status === 'shipped') bg-blue-50 text-blue-600 @elseif($order->status === 'processing') bg-amber-50 text-amber-600 @else bg-gray-100 text-gray-600 @endif">{{ $order->status }}</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-label-sm uppercase tracking-widest text-gray-500 hover:text-black transition-colors">Kelola →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-6">Quick Menu</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.orders.index') }}" class="bg-[#f8f8f8] p-6 rounded-xl transition group hover:bg-black">
            <span class="material-symbols-outlined text-2xl mb-4 block group-hover:text-white">receipt_long</span>
            <p class="font-bold uppercase text-xs tracking-wider group-hover:text-white">Manage Orders</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider group-hover:text-gray-300">Update status & no. resi</p>
        </a>
        <a href="{{ route('admin.products.index') }}" class="bg-[#f8f8f8] p-6 rounded-xl transition group hover:bg-black">
            <span class="material-symbols-outlined text-2xl mb-4 block group-hover:text-white">inventory_2</span>
            <p class="font-bold uppercase text-xs tracking-wider group-hover:text-white">Manage Products</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider group-hover:text-gray-300">Add, edit, delete products</p>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="bg-[#f8f8f8] p-6 rounded-xl transition group hover:bg-black">
            <span class="material-symbols-outlined text-2xl mb-4 block group-hover:text-white">category</span>
            <p class="font-bold uppercase text-xs tracking-wider group-hover:text-white">Manage Categories</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider group-hover:text-gray-300">Create and organize categories</p>
        </a>
        <a href="{{ route('admin.articles.index') }}" class="bg-[#f8f8f8] p-6 rounded-xl transition group hover:bg-black">
            <span class="material-symbols-outlined text-2xl mb-4 block group-hover:text-white">menu_book</span>
            <p class="font-bold uppercase text-xs tracking-wider group-hover:text-white">Manage Articles</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider group-hover:text-gray-300">Halloween, Natal, and more</p>
        </a>
        <a href="{{ route('catalog') }}" target="_blank" class="bg-[#f8f8f8] p-6 rounded-xl transition group hover:bg-black">
            <span class="material-symbols-outlined text-2xl mb-4 block group-hover:text-white">storefront</span>
            <p class="font-bold uppercase text-xs tracking-wider group-hover:text-white">View Catalog</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider group-hover:text-gray-300">See the storefront</p>
        </a>
    </div>
</div>
@endsection