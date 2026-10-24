@extends('layouts.admin')

@section('title', 'Tampilan Web - Information')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="mb-10"><p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Tampilan Web</p><h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Information</h1><p class="mt-2 text-sm text-gray-500">Edit deskripsi, judul, dan isi setiap bagian halaman informasi.</p></div>
@if(session('success'))<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>@endif
@if($errors->any())<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ $errors->first() }}</div>@endif

<form method="POST" action="{{ route('admin.web.information.update') }}">@csrf @method('PUT')
<div class="space-y-8">
@foreach($topics as $slug => $topic)
<div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
    <h2 class="font-headline-lg text-2xl font-extrabold uppercase tracking-tight mb-6">{{ $topic['title'] }}</h2>
    <label class="block font-label-sm text-xs uppercase tracking-widest mb-2">Deskripsi Halaman</label>
    <textarea name="topics[{{ $slug }}][description]" rows="3" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm leading-relaxed mb-8">{{ old("topics.$slug.description", $topic['description']) }}</textarea>
    <div class="space-y-6">
    @foreach($topic['sections'] as $index => $section)
        <div class="border border-gray-100 rounded-xl p-5 bg-[#fafafa]">
            <div class="flex items-center justify-between mb-4"><p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Section</p><button type="button" class="remove-information-section text-[10px] uppercase tracking-widest text-red-500 hover:text-red-700">Hapus</button></div>
            <label class="block font-label-sm text-xs uppercase tracking-widest mb-2">Judul</label>
            <input name="topics[{{ $slug }}][sections][{{ $index }}][heading]" value="{{ old("topics.$slug.sections.$index.heading", $section['heading']) }}" required class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm mb-4">
            <label class="block font-label-sm text-xs uppercase tracking-widest mb-2">Isi</label>
            <textarea name="topics[{{ $slug }}][sections][{{ $index }}][body]" rows="5" required class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm leading-relaxed">{{ old("topics.$slug.sections.$index.body", $section['body']) }}</textarea>
        </div>
    @endforeach
    </div>
    <button type="button" class="add-information-section mt-5 px-5 py-2.5 border border-gray-300 rounded-full font-label-sm text-[10px] uppercase tracking-widest hover:border-black transition" data-topic="{{ $slug }}">Tambah Bagian</button>
</div>
@endforeach
</div>
<div class="mt-8 flex justify-end"><button class="px-8 py-3.5 bg-black text-white rounded-full font-bold uppercase text-xs tracking-widest hover:opacity-80 transition">Simpan Semua Perubahan</button></div>
</form>

<script>
document.addEventListener('click', function (event) {
    const remove = event.target.closest('.remove-information-section');
    if (!remove) return;
    Swal.fire({
        title: 'Hapus section?',
        text: 'Section ini akan dihapus setelah perubahan disimpan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (result.isConfirmed) remove.closest('.border').remove();
    });
});

document.querySelectorAll('.add-information-section').forEach(function (button) {
    button.addEventListener('click', function () {
        const topic = button.dataset.topic;
        const container = button.previousElementSibling;
        const index = container.children.length;
        const block = document.createElement('div');
        block.className = 'border border-gray-100 rounded-xl p-5 bg-[#fafafa]';
        block.innerHTML =             '<div class="flex items-center justify-between mb-4"><p class="font-label-sm text-[10px] uppercase tracking-widest text-gray-400">Section</p><button type="button" class="remove-information-section text-[10px] uppercase tracking-widest text-red-500">Hapus</button></div>' +
            '<label class="block font-label-sm text-xs uppercase tracking-widest mb-2">Judul</label>' +
            '<input name="topics[' + topic + '][sections][' + index + '][heading]" required class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm mb-4">' +
            '<label class="block font-label-sm text-xs uppercase tracking-widest mb-2">Isi</label>' +
            '<textarea name="topics[' + topic + '][sections][' + index + '][body]" rows="5" required class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm leading-relaxed"></textarea>';
        container.appendChild(block);
    });
});
</script>
@endsection
