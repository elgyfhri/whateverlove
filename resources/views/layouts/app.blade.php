<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>@yield('title', 'Catalog') | WHATEVERLOVE</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
</head>
<body class="bg-white text-on-background font-body-md">

  @php
    $cartCount = auth()->check() ? (auth()->user()->cart?->items->sum('qty') ?? 0) : 0;
  @endphp

  <!-- TopNavBar -->
  <header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-6 md:px-12 py-4 bg-white border-b border-gray-100 shadow-sm">
    <div class="flex items-center gap-3 md:gap-5">
      <a href="/" class="flex items-center">
        <img alt="WHATEVERLOVE Logo" class="h-10 md:h-14 object-contain" src="{{ asset('assets/logo.png') }}" />
      </a>
      <nav class="hidden md:flex gap-8 items-center">
        <a class="font-label-sm uppercase tracking-wider text-xs {{ request()->routeIs('catalog') || request()->routeIs('catalog.category') ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-700 font-medium hover:text-black transition-colors' }}" href="{{ route('catalog') }}">CATALOG</a>
        <a class="font-label-sm uppercase tracking-wider text-xs {{ request()->routeIs('article') || request()->routeIs('article.show') ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-700 font-medium hover:text-black transition-colors' }}" href="{{ route('article') }}">ARTICLE</a>
        <a class="font-label-sm uppercase tracking-wider text-xs {{ request()->routeIs('lookbook') || request()->routeIs('lookbook.show') ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-700 font-medium hover:text-black transition-colors' }}" href="{{ route('lookbook') }}">LOOKBOOK</a>
        <a class="font-label-sm uppercase tracking-wider text-xs {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'text-black font-bold border-b-2 border-black pb-1' : 'text-gray-700 font-medium hover:text-black transition-colors' }}" href="{{ route('orders.index') }}">PESANAN</a>
      </nav>
      <button id="mobileMenuBtn" type="button" class="md:hidden material-symbols-outlined text-xl" aria-controls="mobileMenu" aria-expanded="false" aria-label="Open menu">menu</button>
    </div>
    <div class="flex gap-5 items-center text-black">
      @auth
        <div class="relative">
          <button id="accountBtn" class="material-symbols-outlined text-xl hover:opacity-70 transition-opacity cursor-pointer" aria-label="Account">person</button>
          <div id="accountMenu" class="hidden absolute right-0 top-9 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
            <p class="px-4 py-1 text-xs font-bold uppercase tracking-wider text-gray-900 truncate">{{ auth()->user()->name }}</p>
            <div class="my-1 border-t border-gray-100"></div>
            <button type="button" class="w-full text-left px-4 py-2 text-xs font-label-sm uppercase tracking-wider text-gray-600 hover:bg-gray-50 cursor-pointer" onclick="window.__cartDrawer && window.__cartDrawer.open(); this.closest('#accountMenu').classList.add('hidden');">My Cart</button>
            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-xs font-label-sm uppercase tracking-wider text-gray-600 hover:bg-gray-50">My Orders</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="w-full text-left px-4 py-2 text-xs font-label-sm uppercase tracking-wider text-red-500 hover:bg-gray-50">Logout</button>
            </form>
          </div>
        </div>
      @else
        <a class="material-symbols-outlined text-xl hover:opacity-70 transition-opacity" href="{{ route('login') }}" aria-label="Login">person</a>
      @endauth
      <button type="button" id="cartOpenBtn" class="relative flex items-center text-xl hover:opacity-70 transition-opacity cursor-pointer" aria-label="Cart">
        <span class="material-symbols-outlined">shopping_bag</span>
        <span id="cartCountBadge" class="absolute -top-1.5 -right-2.5 bg-secondary text-white text-[9px] font-bold w-4 h-4 rounded-full items-center justify-center {{ $cartCount > 0 ? 'flex' : 'hidden' }}">{{ $cartCount }}</span>
      </button>
    </div>
  </header>

  <nav id="mobileMenu" class="hidden fixed top-[73px] left-0 right-0 z-40 bg-white border-b border-gray-100 shadow-sm px-6 py-5 md:hidden">
    <div class="flex flex-col gap-5 font-label-sm text-xs uppercase tracking-wider">
      <a href="{{ route('catalog') }}">Catalog</a>
      <a href="{{ route('article') }}">Article</a>
      <a href="{{ route('lookbook') }}">Lookbook</a>
      <a href="{{ route('orders.index') }}">Pesanan</a>
    </div>
  </nav>

  <!-- Main -->
  <main class="pt-20">
    @yield('content')
  </main>

  <!-- Footer -->
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
          <li><a class="hover:text-white transition-colors" href="{{ route('contact') }}">Contact Us</a></li>
          <li><a class="hover:text-white transition-colors" href="{{ auth()->user()?->is_admin ? route('admin.dashboard') : route('admin.login') }}">Admin</a></li>
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

  <!-- Cart Drawer -->
  <div id="cartDrawerOverlay" class="fixed inset-0 bg-black/40 z-[80] hidden"></div>
  <aside id="cartDrawer" class="fixed top-0 right-0 h-full w-full max-w-[420px] bg-white z-[90] flex flex-col translate-x-full transition-transform duration-300 ease-out shadow-2xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <p class="font-label-sm text-xs uppercase tracking-widest text-gray-700">Shopping Bag</p>
      <button type="button" id="cartDrawerClose" class="material-symbols-outlined text-xl text-gray-500 hover:text-black transition cursor-pointer" aria-label="Close">close</button>
    </div>
    <div id="cartDrawerFlash" class="hidden mx-5 mt-4 px-4 py-3 rounded-xl text-xs font-label-sm"></div>
    <div id="cartDrawerBody" class="flex-1 flex flex-col min-h-0">
      @include('cart.drawer', ['drawerCart' => $drawerCart])
    </div>
  </aside>

  <script>
    (function () {
        const drawer = document.getElementById('cartDrawer');
        const overlay = document.getElementById('cartDrawerOverlay');
        const body = document.getElementById('cartDrawerBody');
        const badge = document.getElementById('cartCountBadge');
        const openBtn = document.getElementById('cartOpenBtn');
        const closeBtn = document.getElementById('cartDrawerClose');
        const flash = document.getElementById('cartDrawerFlash');

        function open() {
            if (!drawer || !overlay) return;
            overlay.classList.remove('hidden');
            drawer.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            if (!drawer || !overlay) return;
            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function setBadge(count) {
            if (!badge) return;
            count = parseInt(count, 10) || 0;
            badge.textContent = count;
            if (count > 0) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        function showFlash(message, type) {
            if (!flash || !message) return;
            flash.textContent = message;
            flash.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'border', 'border-green-200', 'bg-red-50', 'text-red-700', 'border-red-200');
            if (type === 'error') {
                flash.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
            } else {
                flash.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
            }
            clearTimeout(showFlash._t);
            showFlash._t = setTimeout(function () { flash.classList.add('hidden'); }, 2500);
        }

        function refresh(data, keepOpen) {
            if (!data) return;
            if (typeof data.count !== 'undefined') setBadge(data.count);
            if (data.html) body.innerHTML = data.html;
            if (data.flash) showFlash(data.flash.text, data.flash.type);
            if (keepOpen && typeof data.count !== 'undefined' && data.count === 0) { /* stays open showing empty */ }
        }

        function csrf() {
            const m = document.querySelector('meta[name="csrf-token"]');
            return m ? m.getAttribute('content') : '';
        }

        function api(url, method, payload) {
            const opts = {
                method: method,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            };
            const body = new URLSearchParams();
            if (payload && typeof payload === 'object') {
                Object.keys(payload).forEach(function (k) { body.set(k, payload[k]); });
            }
            if (method !== 'GET') {
                body.set('_token', csrf());
                opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }
            opts.body = body.toString();
            return fetch(url, opts).then(function (res) {
                return res.json().catch(function () { return {}; });
            });
        }

        function handleCartItemClick(e) {
            const container = e.target.closest('[data-cart-item]');
            if (!container) return;

            if (e.target.closest('[data-cart-remove]')) {
                api(container.dataset.remove, 'DELETE', {}).then(function (data) {
                    if (data.redirect) { window.location.href = data.redirect; return; }
                    refresh(data);
                });
                return;
            }

            const dec = e.target.closest('[data-qty-dec]');
            const inc = e.target.closest('[data-qty-inc]');
            const cur = parseInt((dec || inc).dataset.qtyDec || (dec || inc).dataset.qtyInc, 10);
            let qty = cur;
            if (dec) qty = Math.max(1, cur - 1);
            if (inc) qty = Math.min(99, cur + 1);

            if (qty !== cur) {
                api(container.dataset.update, 'PATCH', { qty: qty }).then(function (data) {
                    if (data.redirect) { window.location.href = data.redirect; return; }
                    refresh(data);
                });
            }
        }

        if (openBtn) openBtn.addEventListener('click', open);
        if (closeBtn) closeBtn.addEventListener('click', close);
        if (overlay) overlay.addEventListener('click', close);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
        if (body) body.addEventListener('click', handleCartItemClick);

        window.__cartDrawer = { open: open, close: close, refresh: refresh, setBadge: setBadge };

        document.addEventListener('submit', function (e) {
            const form = e.target.closest('#addToCartForm');
            if (!form) return;
            e.preventDefault();

            const btn = form.querySelector('button[type="submit"]');
            let errorBox = document.getElementById('addToCartError');
            if (!errorBox) {
                errorBox = document.createElement('p');
                errorBox.id = 'addToCartError';
                errorBox.className = 'hidden text-xs text-red-500 font-label-sm';
                form.appendChild(errorBox);
            }
            errorBox.classList.add('hidden');
            if (btn) btn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form),
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, data: data };
                    }).catch(function () {
                        return { ok: res.ok, data: {} };
                    });
                })
                .then(function (r) {
                    const data = r.data;
                    if (data.redirect) { window.location.href = data.redirect; return; }
                    if (data.error || !r.ok) {
                        errorBox.textContent = data.error || 'Gagal menambahkan ke keranjang.';
                        errorBox.classList.remove('hidden');
                        return;
                    }
                    refresh(data);
                    open();
                })
                .finally(function () { if (btn) btn.disabled = false; });
        });

        (function accountMenu() {
            const btn = document.getElementById('accountBtn');
            const menu = document.getElementById('accountMenu');
            if (!btn || !menu) return;
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target)) menu.classList.add('hidden');
            });
        })();

        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuBtn?.addEventListener('click', function () {
            const open = !mobileMenu.classList.toggle('hidden');
            mobileMenuBtn.setAttribute('aria-expanded', String(open));
            mobileMenuBtn.textContent = open ? 'close' : 'menu';
        });
    })();
</script>
</body>
</html>