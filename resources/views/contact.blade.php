@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
{{-- Hero --}}
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden pt-16">
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ asset('assets/contactus.png') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-white"></div>
    </div>
    <div class="relative z-10 text-center px-4">
        <p class="font-label-sm text-xs uppercase tracking-widest text-white/80 mb-3">We want to hear from you</p>
        <h1 class="text-white font-display-xl text-4xl md:text-6xl font-extrabold tracking-tight uppercase drop-shadow-md">
            Contact Us
        </h1>
    </div>
</section>

{{-- Info cards --}}
<section class="py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
        <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-gray-300 transition-colors">
            <span class="material-symbols-outlined text-3xl mb-4 block text-secondary">mail</span>
            <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight mb-2">Email</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Untuk pertanyaan umum, kolaborasi, dan partnership.</p>
            <a href="mailto:hello@whateverlove.id" class="inline-block mt-4 font-label-sm text-xs uppercase tracking-widest underline underline-offset-4 hover:text-secondary transition-colors">hello@whateverlove.id</a>
        </div>

        <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-gray-300 transition-colors">
            <span class="material-symbols-outlined text-3xl mb-4 block text-secondary">send</span>
            <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight mb-2">Instagram</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Follow untuk update drop terbaru dan sekilas di balik layar.</p>
            <a href="https://instagram.com/whateverlove" target="_blank" rel="noopener" class="inline-block mt-4 font-label-sm text-xs uppercase tracking-widest underline underline-offset-4 hover:text-secondary transition-colors">@whateverlove</a>
        </div>

        <div class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-gray-300 transition-colors">
            <span class="material-symbols-outlined text-3xl mb-4 block text-secondary">storefront</span>
            <h3 class="font-headline-lg text-lg font-bold uppercase tracking-tight mb-2">Our Stores</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Selesaikan pesanan lewat marketplace resmi kami.</p>
            <div class="mt-4 space-y-2">
                <a href="https://shopee.co.id/whateverlove" target="_blank" rel="noopener"
                    class="flex items-center justify-between gap-2 px-4 py-2.5 bg-black text-white font-bold uppercase text-[10px] tracking-widest rounded-lg hover:opacity-80 transition">
                    <span>🛍️ Shopee</span>
                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
                <a href="https://www.tokopedia.com/whateverlove_shop" target="_blank" rel="noopener"
                    class="flex items-center justify-between gap-2 px-4 py-2.5 bg-[#3ac569] text-white font-bold uppercase text-[10px] tracking-widest rounded-lg hover:opacity-80 transition">
                    <span>🛒 Tokopedia</span>
                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
                <a href="https://www.bukalapak.com/u/whateverlove" target="_blank" rel="noopener"
                    class="flex items-center justify-between gap-2 px-4 py-2.5 bg-[#e63f38] text-white font-bold uppercase text-[10px] tracking-widest rounded-lg hover:opacity-80 transition">
                    <span>🛒 Bukalapak</span>
                    <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
            </div>
            <a href="{{ route('catalog') }}" class="inline-block mt-4 font-label-sm text-xs uppercase tracking-widest underline underline-offset-4 hover:text-secondary transition-colors">View Catalog</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
        {{-- Form --}}
        <div>
            <h2 class="font-headline-lg text-2xl md:text-3xl font-extrabold uppercase tracking-tight mb-3">Send us a message</h2>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 max-w-md">
                Isi form di bawah dan email akan terbuka otomatis di aplikasi email kamu — atau hubungi kami langsung lewat email di atas.
            </p>
            <form method="get" action="mailto:hello@whateverlove.id" enctype="text/plain" class="space-y-6">
                <div>
                    <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Name</label>
                    <input id="name" name="Name" type="text" required
                        class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm transition-colors"
                        placeholder="Your name">
                </div>
                <div>
                    <label for="email" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Email</label>
                    <input id="email" name="Email" type="email" required
                        class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm transition-colors"
                        placeholder="you@example.com">
                </div>
                <div>
                    <label for="subject" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Subject</label>
                    <input id="subject" name="Subject" type="text" required
                        class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm transition-colors"
                        placeholder="What is this about?">
                </div>
                <div>
                    <label for="message" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Message</label>
                    <textarea id="message" name="Message" rows="5" required
                        class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm transition-colors resize-none"
                        placeholder="Write your message..."></textarea>
                </div>
                <button type="submit"
                    class="px-8 py-3.5 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Send Message
                </button>
            </form>
        </div>

        {{-- Side visual --}}
        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl">
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