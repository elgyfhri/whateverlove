<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->cart();

        return view('cart', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'size' => ['nullable', 'string', 'max:32'],
        ]);

        $size = $this->resolveSize($product, $data['size'] ?? null);

        if ($size === false) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Silakan pilih ukuran produk yang tersedia.'], 422);
            }

            return redirect()->route('product.show', $product)
                ->with('error', 'Silakan pilih ukuran produk yang tersedia.')
                ->withInput();
        }

        if (!auth()->check()) {
            session()->put('cart.pending', [
                'product_id' => $product->id,
                'qty' => $data['qty'],
                'size' => $size,
            ]);

            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('login')]);
            }

            return redirect()->route('login')
                ->with('info', 'Silakan login terlebih dahulu untuk menambahkan ke keranjang.');
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            if ($request->wantsJson()) {
                return response()->json(['redirect' => route('verification.notice')]);
            }

            return redirect()->route('verification.notice')
                ->with('info', 'Verifikasi email kamu terlebih dahulu untuk menambahkan ke keranjang.');
        }

        $cart = $this->cart();

        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        $newQty = ($existing ? $existing->qty : 0) + $data['qty'];

        if (!$this->stockAvailableFor($product, $size, $newQty)) {
            $msg = 'Stok ' . ($size ? '"' . $size . '" ' : '') . 'tidak mencukupi.';

            if ($request->wantsJson()) {
                return $this->drawerResponse($cart, $msg, 'error');
            }

            return redirect()->route('product.show', $product)
                ->with('error', $msg);
        }

        $item = $cart->items()->updateOrCreate(
            ['product_id' => $product->id, 'size' => $size],
            ['qty' => $newQty]
        );

        $msg = $product->name . ($size ? ' (' . $size . ')' : '') . ' ditambahkan ke keranjang.';

        if ($request->wantsJson()) {
            return $this->drawerResponse($cart, $msg);
        }

        return redirect()->route('cart.index')
            ->with('success', $msg);
    }

    public function update(Request $request, CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);

        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = $item->product;

        if (!$this->stockAvailableFor($product, $item->size, $data['qty'])) {
            $msg = 'Stok ' . ($item->size ? '"' . $item->size . '" ' : '') . 'tinggal ' . (int) $this->availableStockFor($product, $item->size) . ' unit.';

            if ($request->wantsJson()) {
                return $this->drawerResponse($this->cart(), $msg, 'error');
            }

            return back()->with('error', $msg);
        }

        $item->update(['qty' => $data['qty']]);

        if ($request->wantsJson()) {
            return $this->drawerResponse($this->cart(), 'Kuantitas diperbarui.');
        }

        return back()->with('success', 'Kuantitas diperbarui.');
    }

    public function remove(Request $request, CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);

        $item->delete();

        if ($request->wantsJson()) {
            return $this->drawerResponse($this->cart(), 'Produk dihapus dari keranjang.');
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    private function cart(): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => auth()->id()],
            ['user_id' => auth()->id()]
        );
    }

    private function drawerResponse(Cart $cart, ?string $flash = null, string $type = 'success'): \Illuminate\Http\JsonResponse
    {
        $cart->load('items.product');

        return response()->json([
            'html' => view('cart.drawer', ['drawerCart' => $cart])->render(),
            'count' => $cart->items->sum('qty'),
            'flash' => $flash ? ['text' => $flash, 'type' => $type] : null,
        ]);
    }

    public function addPending($user, array $pending): void
    {
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        $product = Product::find($pending['product_id']);

        if (!$product || !$product->is_active) {
            return;
        }

        $qty = max(1, min(99, (int) ($pending['qty'] ?? 1)));

        $size = $this->resolveSize($product, $pending['size'] ?? null);

        if ($size === false) {
            return;
        }

        $existing = $cart->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        $newQty = ($existing ? $existing->qty : 0) + $qty;

        if (!$this->stockAvailableFor($product, $size, $newQty)) {
            return;
        }

        $cart->items()->updateOrCreate(
            ['product_id' => $product->id, 'size' => $size],
            ['qty' => $newQty]
        );
    }

    private function resolveSize(Product $product, ?string $requested): string|false|null
    {
        if (!$product->sizes()->exists()) {
            return false;
        }

        $normalized = Str::upper(trim((string) $requested));

        if ($normalized === '') {
            return false;
        }

        $match = $product->sizes->first(fn ($ps) => Str::upper((string) $ps->size) === $normalized);

        return $match ? (string) $match->size : false;
    }

    private function availableStockFor(Product $product, ?string $size): int
    {
        $ps = ProductSize::where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        return $ps ? max(0, $ps->stock) : 0;
    }

    private function stockAvailableFor(Product $product, ?string $size, int $qty): bool
    {
        return $qty <= $this->availableStockFor($product, $size);
    }
}