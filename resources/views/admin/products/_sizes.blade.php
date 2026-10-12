@php
    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

    $parseDims = function (string $label): bool {
        return preg_match('/^\s*\d{1,4}\s*[xX*·]\s*\d{1,4}/', $label) === 1;
    };
@endphp
<div class="border-t border-gray-100 pt-6">
    <h3 class="font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-1">Sizes & Stock <span class="text-red-500">*</span></h3>
    <p class="text-xs text-gray-400 mb-4 font-label-sm">Wajib diisi. Pilih ukuran standar (XS–XXL) di dropdown, <b>atau</b> ketik ukuran sendiri di kolom bawah (mis. 3 x 50). Kolom bawah menang jika diisi. Lalu isi stok.</p>

    <div id="sizesWrap" class="space-y-4">
        @forelse($existingSizes as $ps)
            @php $isCustom = $parseDims((string) $ps->size) && !in_array($ps->size, $sizeOptions, true); @endphp
            <div class="size-row flex flex-wrap items-center gap-3">
                <input type="hidden" name="sizes[{{ $loop->index }}][id]" value="{{ $ps->id }}">
                <div class="flex flex-col gap-2">
                    <select name="sizes[{{ $loop->index }}][size]"
                        class="w-36 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center bg-white">
                        <option value="">— Pilih ukuran —</option>
                        @foreach($sizeOptions as $opt)
                            <option value="{{ $opt }}" {{ !$isCustom && $ps->size === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="sizes[{{ $loop->index }}][custom]" maxlength="32"
                        value="{{ $isCustom ? $ps->size : '' }}" placeholder="3 x 50"
                        class="w-36 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center">
                </div>
                <div class="flex flex-col gap-2">
                    <input type="number" name="sizes[{{ $loop->index }}][stock]" value="{{ $ps->stock }}" min="0" max="9999"
                        class="w-24 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center">
                    <span class="text-xs text-gray-400 font-label-sm text-center">unit stock</span>
                </div>
                <button type="button" class="size-remove ml-auto material-symbols-outlined text-lg text-gray-400 hover:text-red-500 transition" title="Hapus ukuran">close</button>
            </div>
        @empty
            <div class="size-row flex flex-wrap items-center gap-3">
                <div class="flex flex-col gap-2">
                    <select name="sizes[0][size]"
                        class="w-36 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center bg-white">
                        <option value="">— Pilih ukuran —</option>
                        @foreach($sizeOptions as $opt)
                            <option value="{{ $opt }}" {{ $opt === 'M' ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="sizes[0][custom]" maxlength="32" value="" placeholder="3 x 50"
                        class="w-36 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center">
                </div>
                <div class="flex flex-col gap-2">
                    <input type="number" name="sizes[0][stock]" value="0" min="0" max="9999"
                        class="w-24 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center">
                    <span class="text-xs text-gray-400 font-label-sm text-center">unit stock</span>
                </div>
                <button type="button" class="size-remove ml-auto material-symbols-outlined text-lg text-gray-400 hover:text-red-500 transition" title="Hapus ukuran">close</button>
            </div>
        @endforelse
    </div>

    <button type="button" id="addSize"
        class="mt-4 inline-flex items-center gap-1 text-xs font-label-sm uppercase tracking-widest text-black underline underline-offset-4 hover:opacity-70 transition">
        <span class="material-symbols-outlined text-sm">add</span> Add Size
    </button>

    @error('sizes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<script>
    (function () {
        const wrap = document.getElementById('sizesWrap');
        const addBtn = document.getElementById('addSize');
        if (!wrap || !addBtn) return;
        let idx = wrap.querySelectorAll('.size-row').length;
        const rowHtml = function () {
            return '<div class="flex flex-col gap-2">' +
                '<select name="sizes[' + idx + '][size]" class="w-36 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center bg-white">' +
                '<option value="">— Pilih ukuran —</option>' +
                '<option value="XS">XS</option><option value="S">S</option><option value="M" selected>M</option>' +
                '<option value="L">L</option><option value="XL">XL</option><option value="XXL">XXL</option></select>' +
                '<input type="text" name="sizes[' + idx + '][custom]" maxlength="32" value="" placeholder="3 x 50" class="w-36 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center">' +
                '</div>' +
                '<div class="flex flex-col gap-2">' +
                '<input type="number" name="sizes[' + idx + '][stock]" value="0" min="0" max="9999" class="w-24 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm text-center">' +
                '<span class="text-xs text-gray-400 font-label-sm text-center">unit stock</span>' +
                '</div>' +
                '<button type="button" class="size-remove ml-auto material-symbols-outlined text-lg text-gray-400 hover:text-red-500 transition" title="Hapus ukuran">close</button>';
        };

        addBtn.addEventListener('click', function () {
            const div = document.createElement('div');
            div.className = 'size-row flex flex-wrap items-center gap-3';
            div.innerHTML = rowHtml();
            wrap.appendChild(div);
            idx++;
        });

        wrap.addEventListener('click', function (e) {
            if (e.target.classList.contains('size-remove')) {
                e.target.closest('.size-row').remove();
            }
        });
    })();
</script>