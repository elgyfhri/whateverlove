<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Admin Login | WHATEVERLOVE</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=DM+Sans:wght@400;500&family=Space+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "surface-container": "#f4f4f2",
            "background": "#ffffff",
            "on-background": "#1a1c1b",
            "primary": "#000000",
            "secondary": "#b90a5a"
          },
          fontFamily: {
            "label-sm": ["Space Mono"],
            "body-md": ["DM Sans"],
            "display-xl": ["Bricolage Grotesque"],
            "headline-lg": ["Bricolage Grotesque"]
          }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
</head>
<body class="bg-white text-on-background font-body-md min-h-screen flex">

  <!-- Left brand panel -->
  <div class="hidden lg:flex flex-1 items-center justify-center relative overflow-hidden bg-black">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('{{ asset('assets/hero.jpg') }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-black/60"></div>
    <div class="relative z-10 text-center px-12 max-w-lg">
      <img alt="WHATEVERLOVE Logo" class="h-10 object-contain mx-auto mb-8 opacity-90" src="{{ asset('assets/logo.png') }}" />
      <h1 class="font-display-xl text-5xl font-extrabold tracking-tight text-white uppercase drop-shadow-md">
        WHATEVERLOVE
      </h1>
      <p class="mt-4 text-gray-300 font-label-sm text-xs uppercase tracking-widest">
        Admin Dashboard - Manage your canvas of expression
      </p>
    </div>
  </div>

  <!-- Right form -->
  <div class="flex-1 flex items-center justify-center bg-white">
    <div class="w-full max-w-md px-8 py-12">
      <div class="lg:hidden text-center mb-10">
        <img alt="WHATEVERLOVE Logo" class="h-8 object-contain mx-auto mb-6" src="{{ asset('assets/logo.png') }}" />
      </div>

      <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-3">Staff Access</p>
      <h2 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Sign in</h2>
      <p class="mt-2 text-sm text-gray-500">Enter your credentials to manage the store.</p>

      @if($errors->any())
        <div class="mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login') }}" class="mt-10 space-y-6">
        @csrf
        <div>
          <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus
            class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
            placeholder="admin@whateverlove.id">
        </div>
        <div>
          <label class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Password</label>
          <input type="password" name="password" required
            class="w-full bg-transparent border-b-2 border-gray-300 focus:border-black outline-none py-2.5 text-sm placeholder-gray-400 transition-colors"
            placeholder="••••••••">
        </div>
        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 font-label-sm text-xs uppercase tracking-wider text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-black focus:ring-black">
            Remember me
          </label>
          <a href="/" class="font-label-sm text-xs uppercase tracking-wider text-gray-400 hover:text-black transition-colors">← Back to site</a>
        </div>
        <button type="submit"
          class="w-full bg-black text-white font-bold uppercase tracking-widest text-xs py-4 rounded-lg hover:opacity-80 transition">
          Sign In
        </button>
      </form>

      <div class="mt-8 text-center text-xs text-gray-400 font-label-sm">
        Demo: admin@whateverlove.id / password
      </div>
    </div>
  </div>

</body>
</html>