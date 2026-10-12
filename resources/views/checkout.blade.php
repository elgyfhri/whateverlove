@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<section class="py-16 md:py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 font-label-sm text-[10px] uppercase tracking-widest text-gray-500 hover:text-black transition-colors mb-6">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Back to Cart
    </a>

    <div class="mb-12">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Almost there</p>
        <h1 class="font-headline-lg text-4xl md:text-6xl font-extrabold uppercase tracking-tight">Checkout</h1>
    </div>

    @if(session('error'))
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm" class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        @csrf

        {{-- Data diri + alamat --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-8">
                <h2 class="font-headline-lg text-xl font-extrabold uppercase tracking-tight mb-6">Data Diri</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Nama Lengkap <span class="text-secondary">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="255"
                            class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors"
                            placeholder="Nama lengkap penerima">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Email Address <span class="text-secondary">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="255"
                            class="w-full bg-transparent border-b-2 @error('email') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors"
                            placeholder="you@example.com">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">No. HP <span class="text-secondary">*</span></label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="20"
                            class="w-full bg-transparent border-b-2 @error('phone') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors"
                            placeholder="0812xxxxxxx">
                        @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Alamat Lengkap <span class="text-secondary">*</span></label>
                        <textarea id="address" name="address" rows="3" required maxlength="500"
                            class="w-full px-4 py-3 border @error('address') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm resize-y"
                            placeholder="Jalan, no. rumah, RT/RW, patokan, dll.">{{ old('address') }}</textarea>
                        @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-8">
                <h2 class="font-headline-lg text-xl font-extrabold uppercase tracking-tight mb-6">Lokasi Pengiriman</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="province" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Province <span class="text-secondary">*</span></label>
                        <select id="province" name="province_code" required
                            class="w-full px-4 py-3 border @error('province_code') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm bg-white">
                            <option value="">Pilih provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->code }}" {{ old('province_code') === $province->code ? 'selected' : '' }}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="city" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">City <span class="text-secondary">*</span></label>
                        <select id="city" name="city_code" required disabled
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm bg-gray-50 text-gray-400">
                            <option value="">Pilih kota/kabupaten</option>
                        </select>
                        @error('city_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="district" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">District <span class="text-secondary">*</span></label>
                        <select id="district" name="district_code" required disabled
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black outline-none text-sm bg-gray-50 text-gray-400">
                            <option value="">Pilih kecamatan</option>
                        </select>
                        @error('district_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-400 font-label-sm">Estimasi pengiriman (ETD) dihitung sejak paket diserahkan ke kurir, bukan sejak checkout.</p>
                <div class="mt-6 max-w-sm">
                    <label for="postal_code" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Kode Pos</label>
                    <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code') }}" maxlength="10"
                        class="w-full bg-transparent border-b-2 @error('postal_code') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors"
                        placeholder="Opsional, contoh 12440">
                    @error('postal_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 lg:sticky lg:top-32">
            <h2 class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-6">Ringkasan Pesanan</h2>
            <div class="space-y-4 text-sm mb-6">
                <div class="flex justify-between border-b border-gray-100 pb-4">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-bold">IDR {{ number_format($cart->subtotal, 0, '.', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Berat</span>
                    <span class="font-bold" id="weightText">{{ number_format($cart->items->sum(fn($i) => $i->product->weight * $i->qty)) }} g</span>
                </div>

                <div id="shippingBlock">
                    <div class="bg-[#f8f8f8] rounded-xl p-4 text-xs text-gray-600 leading-relaxed mb-3">
                        <p class="font-bold uppercase tracking-widest text-[10px] mb-1">Ongkir</p>
                        <span id="shippingBox">Pilih lokasi pengiriman untuk melihat ongkir.</span>
                    </div>
                    <div id="shippingOptions" class="space-y-2"></div>
                </div>

                <div class="flex justify-between">
                    <span class="font-label-sm uppercase tracking-widest text-xs text-gray-400">Total</span>
                    <span class="font-headline-lg text-xl font-extrabold" id="totalText" data-subtotal="{{ (int) round($cart->subtotal) }}">IDR {{ number_format($cart->subtotal, 0, '.', '.') }}</span>
                </div>
            </div>

            <input type="hidden" name="shipping_cost" id="shippingCost" value="0">
            <input type="hidden" name="shipping_courier" id="shippingCourier" value="">
            <input type="hidden" name="shipping_service" id="shippingService" value="">
            <input type="hidden" name="shipping_estimate" id="shippingEstimate" value="">

            <button type="submit"
                class="w-full px-6 py-4 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                Selesaikan Pesanan
            </button>
            <a href="{{ route('cart.index') }}" class="mt-3 w-full px-6 py-4 text-gray-600 font-label-sm text-xs uppercase tracking-widest rounded-full border border-gray-200 hover:border-black transition flex items-center justify-center">
                Back to Cart
            </a>
        </div>
    </form>
</section>

<script>
    (function () {
        const province = document.getElementById('province');
        const city = document.getElementById('city');
        const district = document.getElementById('district');
        const shippingBox = document.getElementById('shippingBox');
        const shippingOptions = document.getElementById('shippingOptions');
        const shippingCost = document.getElementById('shippingCost');
        const shippingCourier = document.getElementById('shippingCourier');
        const shippingService = document.getElementById('shippingService');
        const shippingEstimate = document.getElementById('shippingEstimate');
        const totalText = document.getElementById('totalText');
        const weightText = document.getElementById('weightText');
        if (!province || !city || !district || !shippingBox) return;

        const subtotal = parseInt(totalText.getAttribute('data-subtotal') || '0', 10);

        const idr = function (n) {
            return 'IDR ' + Number(n).toLocaleString('id-ID');
        };
        const loading = function (sel, msg) {
            sel.innerHTML = '<option value="">' + msg + '</option>';
            sel.disabled = true;
            sel.classList.remove('bg-white', 'text-gray-900');
            sel.classList.add('bg-gray-50', 'text-gray-400');
        };
        const ready = function (sel) {
            sel.disabled = false;
            sel.classList.remove('bg-gray-50', 'text-gray-400');
            sel.classList.add('bg-white', 'text-gray-900');
        };
        const base = function (url, params) {
            return fetch(url + (params ? '?' + new URLSearchParams(params) : ''), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            }).then(function (r) { return r.json(); });
        };

        const etaBufferDays = {{ (int) config('shipping.eta_buffer_days', 2) }};

        const etaTotal = function (etd) {
            var m = String(etd).match(/(\d+)\s*(?:-|\u2013|sampai)\s*(\d+)\s*(?:hari|day|days)\b/i)
                || String(etd).match(/(\d+)\s*(?:hari|day|days)\b/i);
            var days = m ? parseInt(m[m.length - 1], 10) : 0;
            return days + etaBufferDays;
        };

        const selectShipping = function (cost, courier, service, estimate) {
            shippingCost.value = cost;
            shippingCourier.value = courier;
            shippingService.value = service;
            shippingEstimate.value = estimate;
            totalText.textContent = idr(subtotal + cost);
        };

        const renderShipping = function (data) {
            if (!data.ok || !data.costs || data.costs.length === 0) {
                shippingBox.textContent = data.message || 'Tidak ada layanan tersedia.';
                shippingOptions.innerHTML = '';
                selectShipping(0, '', '', '');
                return;
            }

            shippingBox.innerHTML =
                '<span class="font-bold">' + data.destination + '</span> · ' + idr(data.cheapest.cost) +
                ' (' + data.cheapest.name + ' ' + data.cheapest.service + ' · ETD ± ' + etaTotal(data.cheapest.etd) + ' hari)' +
                (data.stale ? ' <span class="text-amber-600 text-xs font-semibold">— harga estimasi</span>' : '');
            if (weightText) weightText.textContent = Number(data.weight).toLocaleString('id-ID') + ' g';

            shippingOptions.innerHTML = data.costs.map(function (c) {
                return '<label class="flex items-start gap-3 border border-gray-100 rounded-xl p-3 cursor-pointer hover:border-black transition select-none">' +
                    '<input type="radio" name="shipping_choice" value="' + c.cost + '" data-courier="' + c.code + '" data-service="' + c.service + '" data-estimate="' + c.etd + '" class="mt-0.5 accent-black">' +
                    '<span class="flex-grow min-w-0">' +
                        '<span class="block text-sm font-bold leading-tight">' + c.name + ' ' + c.service + '</span>' +
                        '<span class="block text-xs text-gray-400 mt-0.5">' + (c.description || 'Pengiriman reguler') + '</span>' +
                        '<span class="block text-xs text-gray-400 mt-0.5">' + idr(c.cost) + (c.etd ? ' · ETD ± ' + etaTotal(c.etd) + ' hari' : '') + '</span>' +
                    '</span>' +
                '</label>';
            }).join('');

            shippingOptions.querySelectorAll('input[name="shipping_choice"]').forEach(function (input) {
                input.addEventListener('change', function () {
                    selectShipping(
                        parseInt(input.value, 10),
                        input.getAttribute('data-courier'),
                        input.getAttribute('data-service'),
                        input.getAttribute('data-estimate')
                    );
                });
            });

            const first = shippingOptions.querySelector('input[name="shipping_choice"]');
            if (first) { first.checked = true; first.dispatchEvent(new Event('change')); }
        };

        const loadShipping = function () {
            if (!province.value || !city.value || !district.value) return;
            shippingBox.textContent = 'Menghitung ongkir…';
            shippingOptions.innerHTML = '';
            base('{{ route('checkout.cost') }}', {
                province_code: province.value,
                city_code: city.value,
                district_code: district.value,
            }).then(renderShipping).catch(function () {
                shippingBox.textContent = 'Gagal menghitung ongkir. Coba pilih waktu lain.';
            });
        };

        province.addEventListener('change', function () {
            district.innerHTML = '<option value="">Pilih kecamatan</option>';
            district.disabled = true;
            loading(city, 'Memuat kota…');
            if (!province.value) { ready(city); city.innerHTML = '<option value="">Pilih kota/kabupaten</option>'; shippingBox.textContent = 'Pilih lokasi pengiriman untuk melihat ongkir.'; return; }
            base('{{ route('checkout.cities') }}', { province_code: province.value }).then(function (rows) {
                city.innerHTML = '<option value="">Pilih kota/kabupaten</option>' +
                    rows.map(function (r) { return '<option value="' + r.code + '">' + r.name + '</option>'; }).join('');
                ready(city);
            });
        });

        city.addEventListener('change', function () {
            loading(district, 'Memuat kecamatan…');
            if (!city.value) { ready(district); district.innerHTML = '<option value="">Pilih kecamatan</option>'; shippingBox.textContent = 'Pilih lokasi pengiriman untuk melihat ongkir.'; return; }
            base('{{ route('checkout.districts') }}', { city_code: city.value }).then(function (rows) {
                district.innerHTML = '<option value="">Pilih kecamatan</option>' +
                    rows.map(function (r) { return '<option value="' + r.code + '">' + r.name + '</option>'; }).join('');
                ready(district);
            });
        });

        district.addEventListener('change', loadShipping);

        @if(old('province_code') && old('city_code'))
            // restore on validation errors
            city.disabled = false;
            base('{{ route('checkout.cities') }}', { province_code: '{{ old('province_code') }}' }).then(function (rows) {
                city.innerHTML = '<option value="">Pilih kota/kabupaten</option>' +
                    rows.map(function (r) { return '<option value="' + r.code + '">' + r.name + '</option>'; }).join('');
                city.value = '{{ old('city_code') }}';
                base('{{ route('checkout.districts') }}', { city_code: city.value }).then(function (rows) {
                    district.innerHTML = '<option value="">Pilih kecamatan</option>' +
                        rows.map(function (r) { return '<option value="' + r.code + '">' + r.name + '</option>'; }).join('');
                    district.value = '{{ old('district_code') }}';
                    district.disabled = false;
                    if ('{{ old('district_code') }}') setTimeout(loadShipping, 50);
                });
            });
        @endif
    })();
</script>
@endsection