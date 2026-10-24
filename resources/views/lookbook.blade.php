@extends('layouts.app')

@section('title', 'Lookbook')

@section('content')
{{-- Hero (same style as home/article) --}}
<section class="py-16 md:py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Editorial stories</p>
    <h1 class="font-headline-lg text-4xl md:text-6xl font-extrabold uppercase tracking-tight">Lookbook</h1>
    <p class="mt-4 text-sm md:text-base text-gray-500 max-w-2xl leading-relaxed">
        The art of expression in every stitch — curated looks, told through our latest drops.
    </p>
</section>

@if($looks->isEmpty())
    <section class="py-16 px-6 md:px-12 max-w-[1440px] mx-auto">
        <div class="text-center py-24 bg-[#f8f8f8] rounded-2xl">
            <div class="text-5xl mb-4">✎</div>
            <h2 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No looks yet</h2>
            <p class="mt-2 text-gray-500 text-sm">New editorial looks are coming soon.</p>
        </div>
    </section>
@else
    {{-- Look grid --}}
    <section class="pb-16 md:pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-12">
            @foreach($looks as $look)
                <a href="{{ route('lookbook.show', $look) }}" class="group cursor-pointer">
                    <div class="relative aspect-[3/2] overflow-hidden rounded-lg bg-[#f8f8f8]">
                        <div class="absolute inset-0 bg-contain md:bg-cover bg-center bg-no-repeat group-hover:scale-105 transition-transform duration-700"
                            style="background-image: url('{{ $look->cover_url }}')"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                       
                        <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-3 text-white">
                            <h3 class="font-headline-lg text-lg md:text-xl font-extrabold uppercase tracking-tight group-hover:underline">
                                {{ $look->name }}
                            </h3>
                            <span class="material-symbols-outlined text-lg shrink-0 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform">arrow_outward</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($looks->hasPages())
            <div class="mt-16 flex justify-center">
                {{ $looks->links('pagination.lookbook') }}
            </div>
        @endif
    </section>
@endif
@endsection