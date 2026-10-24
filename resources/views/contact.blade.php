@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
{{-- Hero --}}
<section class="min-h-[32vh] md:min-h-[40vh] flex items-end bg-white pt-16">
    <div class="w-full max-w-[1440px] mx-auto px-6 md:px-12 pb-12 md:pb-16">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">We want to hear from you</p>
        <h1 class="text-black font-display-xl text-5xl md:text-7xl font-extrabold tracking-tight uppercase">Contact Us</h1>
    </div>
</section>

{{-- Info cards --}}
<section class="py-14 md:py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-200 border border-gray-200 mb-20">
        <div class="group bg-white p-7 md:p-8 hover:bg-[#fafafa] transition-colors">
            <span class="material-symbols-outlined text-3xl mb-4 block text-secondary">mail</span>
            <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight mb-2">Email</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Untuk pertanyaan umum, kolaborasi, dan partnership.</p>
            <a href="mailto:{{ $contact['email'] ?? 'hello@whateverlove.id' }}" class="inline-block mt-4 font-label-sm text-xs uppercase tracking-widest underline underline-offset-4 hover:text-secondary transition-colors">{{ $contact['email'] ?? 'hello@whateverlove.id' }}</a>
        </div>

        <div class="group bg-white p-7 md:p-8 hover:bg-[#fafafa] transition-colors">
            <span class="material-symbols-outlined text-3xl mb-4 block text-secondary">send</span>
            <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight mb-2">Instagram</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Follow untuk update drop terbaru dan sekilas di balik layar.</p>
            <a href="{{ $contact['instagram'] ?? 'https://instagram.com/whateverlove' }}" target="_blank" rel="noopener" class="inline-block mt-4 font-label-sm text-xs uppercase tracking-widest underline underline-offset-4 hover:text-secondary transition-colors">@whateverlove</a>
        </div>

        <div class="group bg-white p-7 md:p-8 hover:bg-[#fafafa] transition-colors">
            <span class="material-symbols-outlined text-3xl mb-4 block text-secondary">storefront</span>
            <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight mb-2">Our Stores</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Selesaikan pesanan lewat marketplace resmi kami.</p>
            <div class="mt-4 space-y-2">
                <a href="{{ $contact['shopee'] ?? 'https://shopee.co.id/whateverlove' }}" target="_blank" rel="noopener"
                    class="flex items-center justify-between gap-2 px-4 py-2.5 bg-black text-white font-bold uppercase text-[10px] tracking-widest rounded-lg hover:opacity-80 transition">
                    <span>🛍️ Shopee</span>
                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
                <a href="{{ $contact['tokopedia'] ?? 'https://www.tokopedia.com/whateverlove_shop' }}" target="_blank" rel="noopener"
                    class="flex items-center justify-between gap-2 px-4 py-2.5 bg-[#3ac569] text-white font-bold uppercase text-[10px] tracking-widest rounded-lg hover:opacity-80 transition">
                    <span>🛒 Tokopedia</span>
                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
                <a href="{{ $contact['bukalapak'] ?? 'https://www.bukalapak.com/u/whateverlove' }}" target="_blank" rel="noopener"
                    class="flex items-center justify-between gap-2 px-4 py-2.5 bg-[#e63f38] text-white font-bold uppercase text-[10px] tracking-widest rounded-lg hover:opacity-80 transition">
                    <span>🛒 Bukalapak</span>
                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
            </div>
            <a href="{{ route('catalog') }}" class="inline-block mt-4 font-label-sm text-xs uppercase tracking-widest underline underline-offset-4 hover:text-secondary transition-colors">View Catalog</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-0 items-stretch border border-gray-200">
        {{-- Side visual --}}
        <div class="relative min-h-[360px] lg:min-h-full overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('assets/ourstories.png') }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-6 left-6 right-6 text-white">
                <p class="font-headline-lg text-xl font-extrabold uppercase tracking-tight">WHATEVERLOVE</p>
                <p class="mt-1 text-xs text-white/80 font-label-sm uppercase tracking-widest">A canvas of expression</p>
            </div>
        </div>
    </div>
</section>
@endsection