@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<section class="px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[calc(100vh-100px)] items-center gap-12">
        <div class="relative aspect-[4/3] lg:aspect-auto lg:h-full min-h-[380px] overflow-hidden rounded-2xl hidden lg:block">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('assets/ourstories.png') }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-8 left-8 right-8 text-white">
                <p class="font-headline-lg text-2xl font-extrabold uppercase tracking-tight">Verifikasi Email</p>
                <p class="mt-2 text-sm text-white/80 leading-relaxed">Konfirmasi email kamu untuk melanjutkan ke checkout dan pembelian.</p>
            </div>
        </div>

        <div class="py-16 lg:py-24 max-w-md mx-auto w-full">
            <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Account</p>
            <h1 class="font-headline-lg text-3xl md:text-4xl font-extrabold uppercase tracking-tight mb-2">Cek Email Kamu</h1>
            <p class="text-sm text-gray-500 mb-6">Kami sudah mengirim tautan verifikasi ke <strong class="text-gray-800">{{ auth()->user()->email }}</strong>. Klik tautan tersebut untuk mengaktifkan akun kamu.</p>

            @if (session('status') === 'verification-link-sent')
                <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-6">
                    Tautan verifikasi baru sudah dikirim ke email kamu.
                </p>
            @endif

            <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-6">
                <strong>Belum sampai?</strong> Cek juga folder <strong>Spam/Promosi</strong> di email kamu — kadang email verifikasi masuk ke sana.
            </div>

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-6">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-4 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-500">Setelah verifikasi, kamu bisa lanjut ke <a href="{{ route('cart.index') }}" class="text-secondary font-medium hover:underline">keranjang</a> atau <a href="{{ route('catalog') }}" class="text-secondary font-medium hover:underline">katalog</a>.</p>
        </div>
    </div>
</section>
@endsection