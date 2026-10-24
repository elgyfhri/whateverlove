<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>{{ $title ?? 'Admin' }} | WHATEVERLOVE Admin</title>
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
<body class="bg-[#f6f6f5] text-on-background font-body-md min-h-screen flex flex-col">

  <!-- TopNavBar -->
  <header class="fixed top-0 left-0 w-full z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-x-8 gap-y-2 px-6 md:px-12 py-4">
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
          <img alt="WHATEVERLOVE Logo" class="h-10 md:h-12 object-contain" src="{{ asset('assets/logo.png') }}" />
        </a>
        <span class="font-label-sm text-[10px] uppercase tracking-widest bg-black text-white px-2 py-1 rounded-sm">Admin</span>
      </div>

      <button id="adminMenuButton" type="button" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-black" aria-controls="adminMenu" aria-expanded="false" aria-label="Open menu">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <nav id="adminMenu" class="hidden md:flex absolute md:static top-full left-0 right-0 bg-white md:bg-transparent border-b md:border-0 border-gray-100 px-6 md:px-0 py-5 md:py-0 flex-col md:flex-row items-start md:items-center gap-5 md:gap-x-6 md:gap-y-2 font-label-sm text-xs uppercase tracking-wider">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Dashboard</a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Orders</a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Products</a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Categories</a>
        <a href="{{ route('admin.articles.index') }}" class="{{ request()->routeIs('admin.articles.*') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Articles</a>
        <a href="{{ route('admin.looks.index') }}" class="{{ request()->routeIs('admin.looks.*') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Lookbook</a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'text-black font-bold border-b-2 border-black pb-0.5' : 'text-gray-500 hover:text-black transition-colors' }}">Users</a>
        <div class="relative group">
          <button type="button" class="text-gray-500 hover:text-black transition-colors">Tampilan Web</button>
          <div class="hidden group-hover:block absolute right-0 md:left-0 md:right-auto top-full pt-3 z-50">
            <div class="bg-white border border-gray-100 shadow-lg rounded-xl p-3 min-w-44 space-y-2 normal-case">
              <a href="{{ route('admin.home-sections.index') }}" class="block px-3 py-2 hover:bg-gray-50 rounded-lg">Dashboard</a>
              <a href="{{ route('admin.web.contact') }}" class="block px-3 py-2 hover:bg-gray-50 rounded-lg">Contact Us</a>
              <a href="{{ route('admin.web.information') }}" class="block px-3 py-2 hover:bg-gray-50 rounded-lg">Information</a>
            </div>
          </div>
        </div>
        <li class="list-none md:hidden pt-3 border-t border-gray-100 w-full">
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="font-label-sm text-xs uppercase tracking-wider text-red-600">Logout</button>
          </form>
        </li>
      </nav>

      <div class="hidden md:flex gap-5 items-center text-black">
        <a class="font-label-sm text-xs uppercase tracking-wider text-gray-500 hover:text-black transition-colors" href="{{ route('catalog') }}" target="_blank">View Site</a>
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="font-label-sm text-xs uppercase tracking-wider px-4 py-2 border border-gray-300 rounded-full hover:border-black hover:text-black transition-colors">
            Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="flex-grow max-w-[1440px] mx-auto px-6 md:px-12 pt-24 pb-16 w-full">
    @if(session('success'))
      <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-body-md">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-body-md">
        {{ session('error') }}
      </div>
    @endif
    @yield('content')
  </main>

  <!-- Footer (same as storefront) -->
  <footer class="w-full bg-[#111111] text-white pt-16 pb-12 px-6 md:px-12 border-t border-gray-800">
    <div class="max-w-[1440px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
      <div class="lg:col-span-2 space-y-4">
        <span class="font-headline-lg text-2xl font-extrabold tracking-tight block">WHATEVERLOVE</span>
        <p class="text-gray-400 text-xs max-w-sm leading-relaxed">
          Creative freedom, uncompromised quality, and the art of expression in every stitch.
        </p>
      </div>

      <div>
        <h5 class="text-gray-200 font-bold mb-4 font-label-sm uppercase tracking-widest text-xs">About</h5>
        <ul class="space-y-2 text-xs text-gray-400 font-label-sm uppercase">
          <li><a class="hover:text-white transition-colors" href="/#about">ABOUT WHATEVERLOVE</a></li>
          <li><a class="hover:text-white transition-colors" href="{{ route('catalog') }}">Article</a></li>
        </ul>
      </div>

      <div>
        <h5 class="text-gray-200 font-bold mb-4 font-label-sm uppercase tracking-widest text-xs">Shop</h5>
        <ul class="space-y-2 text-xs text-gray-400 font-label-sm uppercase">
          @foreach(\App\Models\Category::orderBy('name')->take(3)->get() as $category)
            <li><a class="hover:text-white transition-colors" href="{{ route('catalog.category', $category->slug) }}">{{ $category->name }}</a></li>
          @endforeach
        </ul>
      </div>

      <div>
        <h5 class="text-gray-200 font-bold mb-4 font-label-sm uppercase tracking-widest text-xs">Customer Care</h5>
        <ul class="space-y-2 text-xs text-gray-400 font-label-sm uppercase">
          <li><a class="hover:text-white transition-colors" href="{{ route('catalog') }}">How to Order</a></li>
          <li><a class="hover:text-white transition-colors" href="{{ route('admin.dashboard') }}">Admin</a></li>
        </ul>
      </div>
    </div>

    <div class="max-w-[1440px] mx-auto mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500 font-label-sm uppercase">
      <p>WHATEVERLOVE © 2026. ALL RIGHTS RESERVED.</p>
      <div class="flex gap-6">
        <a class="hover:text-gray-300 transition-colors" href="#">Terms</a>
        <a class="hover:text-gray-300 transition-colors" href="#">Cookies</a>
      </div>
    </div>
  </footer>

  <script>
    const adminMenuButton = document.getElementById('adminMenuButton');
    const adminMenu = document.getElementById('adminMenu');

    adminMenuButton?.addEventListener('click', () => {
      const isOpen = !adminMenu.classList.toggle('hidden');
      adminMenuButton.setAttribute('aria-expanded', String(isOpen));
      adminMenuButton.querySelector('span').textContent = isOpen ? 'close' : 'menu';
    });

    adminMenu?.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        adminMenu.classList.add('hidden');
        adminMenuButton?.setAttribute('aria-expanded', 'false');
        adminMenuButton?.querySelector('span').replaceChildren(document.createTextNode('menu'));
      });
    });
  </script>
</body>
</html>