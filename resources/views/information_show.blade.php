@extends('layouts.app')

@section('title', $topic['title'])

@section('content')
{{-- Hero --}}
<section class="pt-16 md:pt-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <a href="{{ route('information') }}" class="inline-flex items-center gap-2 font-label-sm text-[10px] uppercase tracking-widest text-gray-500 hover:text-black transition-colors mb-6">
        <span class="material-symbols-outlined text-sm">arrow_back</span> All Topics
    </a>
    <div class="flex items-start gap-4 mb-3">
        <span class="material-symbols-outlined text-4xl text-secondary">{{ $topic['icon'] }}</span>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 pt-2">{{ $topic['note'] }}</p>
    </div>
    <h1 class="font-headline-lg text-4xl md:text-6xl font-extrabold uppercase tracking-tight">{{ $topic['title'] }}</h1>
    <p class="mt-4 text-sm md:text-base text-gray-500 max-w-2xl leading-relaxed">{{ $topic['description'] }}</p>
</section>

{{-- Content --}}
<section class="py-16 pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-10">
            @foreach($topic['sections'] as $section)
                <div class="border-b border-gray-100 pb-8">
                    <h2 class="font-headline-lg text-xl md:text-2xl font-extrabold uppercase tracking-tight mb-4">{{ $section['heading'] }}</h2>
                    <p class="text-sm md:text-base text-gray-600 leading-relaxed">{{ $section['body'] }}</p>
                </div>
            @endforeach

            <div class="bg-[#f8f8f8] rounded-2xl p-8">
                <h3 class="font-headline-lg text-lg font-extrabold uppercase tracking-tight mb-2">Masih ada pertanyaan?</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-5">Hubungi kami dan tim WHATEVERLOVE akan segera membantu kamu.</p>
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Contact Us <span class="material-symbols-outlined text-sm">arrow_outward</span>
                </a>
            </div>
        </div>

        <aside class="lg:col-span-1 lg:sticky lg:top-32 h-fit">
            <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-4">Other Topics</p>
            <div class="space-y-3">
                @foreach($topics as $slug => $other)
                    @if($slug !== request()->route('slug'))
                        <a href="{{ route('information.show', $slug) }}" class="flex items-center justify-between bg-white rounded-xl border border-gray-100 px-5 py-4 hover:border-gray-300 transition-colors group">
                            <span class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-xl text-gray-400 group-hover:text-secondary transition-colors">{{ $other['icon'] }}</span>
                                <span class="font-headline-lg text-sm font-bold uppercase tracking-tight">{{ $other['title'] }}</span>
                            </span>
                            <span class="material-symbols-outlined text-sm text-gray-400 group-hover:translate-x-1 transition-transform">arrow_outward</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </aside>
    </div>
</section>
@endsection