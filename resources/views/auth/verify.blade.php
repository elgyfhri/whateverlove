<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | WHATEVERLOVE</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=DM+Sans:wght@400;500&family=Space+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#f6f6f5] text-[#1a1c1b]" style="font-family:'DM Sans',sans-serif">
    <main class="min-h-screen grid lg:grid-cols-2">
        <section class="hidden lg:flex relative min-h-screen bg-black overflow-hidden">
            <img src="{{ asset('assets/hero.jpg') }}" alt="WHATEVERLOVE" class="absolute inset-0 w-full h-full object-cover opacity-30">
            <div class="relative z-10 self-center w-full p-12 text-center text-white">
                <img src="{{ asset('assets/logo.png') }}" alt="WHATEVERLOVE Logo" class="h-16 object-contain mx-auto mb-8 opacity-90">
                <p class="text-3xl font-extrabold uppercase tracking-tight" style="font-family:'Bricolage Grotesque',sans-serif">Verifikasi Email</p>
                <p class="mt-3 max-w-md mx-auto text-sm text-white/75">Konfirmasi email kamu untuk melanjutkan ke checkout dan pembelian.</p>
            </div>
        </section>

        <section class="min-h-screen flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <a href="{{ route('catalog') }}" class="block mb-2 -ml-3 translate-y-2"><img src="{{ asset('assets/logo.png') }}" alt="WHATEVERLOVE Logo" class="h-16 object-contain"></a>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 mb-3" style="font-family:'Space Mono',monospace">Account</p>
                <h1 class="text-4xl font-extrabold uppercase tracking-tight mb-3" style="font-family:'Bricolage Grotesque',sans-serif">Cek Email Kamu</h1>
<p class="text-sm text-gray-500 mb-6">Kami sudah mengirim kode verifikasi 6 digit ke <strong class="text-gray-800">{{ auth()->user()->email }}</strong>. Masukkan kode tersebut di bawah.</p>

                 @if (session('status') === 'verification-link-sent')
                     <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-6">Kode verifikasi baru sudah dikirim. Kode berlaku 1 jam.</p>
                 @endif

                 @if ($errors->has('code'))
                     <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-6">{{ $errors->first('code') }}</p>
                @endif

                <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-6"><strong>Belum sampai?</strong> Cek folder <strong>Spam/Promosi</strong>.</div>

<form method="POST" action="{{ route('verification.verify') }}" class="mb-4">
                     @csrf
                     <input type="text" name="code" inputmode="numeric" maxlength="6" required class="w-full px-5 py-4 border border-gray-300 rounded-full text-center text-2xl tracking-[0.5em]" placeholder="000000" autocomplete="one-time-code">
                     <button type="submit" class="w-full mt-3 px-6 py-4 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">Verifikasi Email</button>
                 </form>

                 <form method="POST" action="{{ route('verification.send') }}">
                     @csrf
                     <button type="submit" class="w-full px-6 py-4 border border-black text-black font-bold uppercase text-xs tracking-widest rounded-full hover:bg-black hover:text-white transition">Kirim Ulang Kode</button>
                 </form>

                <p class="mt-6 text-sm text-gray-500">Setelah verifikasi, kamu bisa lanjut ke <a href="{{ route('cart.index') }}" class="text-secondary font-medium hover:underline">keranjang</a> atau <a href="{{ route('catalog') }}" class="text-secondary font-medium hover:underline">katalog</a>.</p>
            </div>
        </section>
    </main>
</body>
</html>
