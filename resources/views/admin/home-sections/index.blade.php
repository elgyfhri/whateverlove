@extends('layouts.admin')

@section('title', 'Home Sections')

@section('content')
<div class="mb-10">
    <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Homepage</p>
    <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Home Sections</h1>
    <p class="mt-2 text-sm text-gray-500">Kelola gambar, judul, dan link dari 3 panel di bawah bagian About Us pada halaman beranda.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    @foreach($sections as $section)
        <div class="bg-white rounded-2xl border border-gray-100 p-8 overflow-hidden">
            <form method="POST" action="{{ route('admin.home-sections.update', $section) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">{{ $section->key }}</label>
                    <div class="relative aspect-[3/2] rounded-xl overflow-hidden bg-[#f8f8f8] mb-3">
                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $section->cover_url }}')"></div>
                    </div>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-black file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest hover:file:opacity-80 transition">
                    <p class="mt-2 text-xs text-gray-400 font-label-sm">JPG, PNG, WebP. Max 2MB.</p>
                    @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @if($section->image)
                        <label class="mt-3 flex items-center gap-2 font-label-sm text-[10px] uppercase tracking-widest text-gray-500">
                            <input type="checkbox" name="remove_image" value="1" class="w-4 h-4 text-black rounded focus:ring-black">
                            Hapus gambar (pakai default)
                        </label>
                    @endif
                </div>

                <div>
                    <label for="title_{{ $section->id }}" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Title</label>
                    <input id="title_{{ $section->id }}" type="text" name="title" value="{{ old('title', $section->title) }}" required
                        class="w-full bg-transparent border-b-2 @error('title') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="link_text_{{ $section->id }}" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Link Text</label>
                    <input id="link_text_{{ $section->id }}" type="text" name="link_text" value="{{ old('link_text', $section->link_text) }}"
                        class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm transition-colors"
                        placeholder="e.g. Learn More">
                </div>

                <div>
                    <label for="link_{{ $section->id }}" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Link</label>
                    <input id="link_{{ $section->id }}" type="text" name="link" value="{{ old('link', $section->link) }}"
                        class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm transition-colors"
                        placeholder="e.g. /catalog, /contact, /#about">
                    @error('link') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Save Section
                </button>
            </form>
        </div>
    @endforeach
</div>
@endsection