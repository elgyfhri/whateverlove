@extends('layouts.app')

@section('title', 'Article')

@section('content')
{{-- Hero --}}
<section class="py-16 md:py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Collections & themes</p>
    <h1 class="font-headline-lg text-4xl md:text-6xl font-extrabold uppercase tracking-tight">Articles</h1>
    <p class="mt-4 text-sm md:text-base text-gray-500 max-w-2xl leading-relaxed">
        Pilih artikel untuk melihat koleksinya — dari Halloween sampai Natal.
    </p>
</section>

@if($articles->isEmpty())
    <section class="py-16 px-6 md:px-12 max-w-[1440px] mx-auto">
        <div class="text-center py-24 bg-[#f8f8f8] rounded-2xl">
            <div class="text-5xl mb-4">✎</div>
            <h2 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No articles yet</h2>
            <p class="mt-2 text-gray-500 text-sm">New collections are coming soon.</p>
        </div>
    </section>
@else
    {{-- Article gallery --}}
    <section class="pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($articles as $article)
                <a href="{{ route('article.show', $article) }}" class="group cursor-pointer">
                    <div class="relative aspect-[3/4] overflow-hidden rounded-lg bg-[#f8f8f8] mb-5">
                        <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-700" style="background-image: url('{{ $article->image ? asset('storage/' . $article->image) : asset('assets/hero.jpg') }}')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 font-label-sm text-[10px] uppercase tracking-widest bg-white/90 text-black px-3 py-1 rounded-full">
                            {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-3 text-white">
                            <h2 class="font-headline-lg text-xl md:text-2xl font-extrabold uppercase tracking-tight group-hover:underline">
                                {{ $article->name }}
                            </h2>
                            <span class="material-symbols-outlined text-lg shrink-0 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform">arrow_outward</span>
                        </div>
                    </div>
                    @if($article->description)
                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $article->description }}</p>
                    @endif
                    <p class="mt-2 font-label-sm text-[10px] uppercase tracking-widest text-gray-400">{{ $article->active_products_count }} products</p>
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection