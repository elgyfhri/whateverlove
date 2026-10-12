@extends('layouts.app')

@section('title', 'Information')

@section('content')
{{-- Hero --}}
<section class="py-16 md:py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Help center</p>
    <h1 class="font-headline-lg text-4xl md:text-6xl font-extrabold uppercase tracking-tight">Information</h1>
    <p class="mt-4 text-sm md:text-base text-gray-500 max-w-2xl leading-relaxed">
        Temukan jawaban seputar cara memesan, pertanyaan umum, dan kebijakan retur kami.
    </p>
</section>

{{-- Topic cards --}}
<section class="pb-24 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($topics as $slug => $topic)
            <a href="{{ route('information.show', $slug) }}" class="group bg-white rounded-2xl border border-gray-100 p-8 hover:border-gray-300 transition-colors flex flex-col">
                <span class="material-symbols-outlined text-4xl mb-6 block text-secondary group-hover:scale-110 transition-transform">{{ $topic['icon'] }}</span>
                <p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400 mb-2">{{ $topic['note'] }}</p>
                <h2 class="font-headline-lg text-xl md:text-2xl font-extrabold uppercase tracking-tight mb-3 group-hover:underline underline-offset-4">
                    {{ $topic['title'] }}
                </h2>
                <p class="text-sm text-gray-500 leading-relaxed flex-grow">{{ $topic['description'] }}</p>
                <span class="mt-6 inline-flex items-center gap-2 font-label-sm text-xs uppercase tracking-widest text-black group-hover:text-secondary transition-colors">
                    Read More <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_outward</span>
                </span>
            </a>
        @endforeach
    </div>
</section>
@endsection