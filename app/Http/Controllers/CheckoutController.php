<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductSize;
use App\Services\MidtransService;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = $this->cart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $cart->load('items.product');
        $provinces = $this->allowedProvinces();

        return view('checkout', compact('cart', 'provinces'));
    }

    public function provinces()
    {
        return response()->json($this->allowedProvinces());
    }

    public function cities(Request $request)
    {
        $request->validate(['province_code' => ['required', 'string', 'max:2']]);

        return response()->json(
            City::where('province_code', $request->province_code)
                ->orderBy('name')
                ->get(['code', 'name', 'province_code'])
        );
    }

    public function districts(Request $request)
    {
        $request->validate(['city_code' => ['required', 'string', 'max:4']]);

        return response()->json(
            District::where('city_code', $request->city_code)
                ->orderBy('name')
                ->get(['code', 'name'])
        );
    }

    public function cost(Request $request)
    {
        $request->validate([
            'province_code' => ['required', 'string', 'max:2'],
            'city_code' => ['required', 'string', 'max:4'],
            'district_code' => ['required', 'string', 'max:6'],
        ]);

        $service = app(RajaOngkirService::class);

        if (!$service->available()) {
            return response()->json(['ok' => false, 'message' => 'Layanan ongkir belum aktif.']);
        }

        $cart = $this->cart();
        $cart->load('items.product');

        $weight = (int) $cart->items->sum(fn ($i) => (int) $i->product->weight * $i->qty);
        $weight = max(1, (int) ceil($weight / 1000) * 1000);

        $district = District::where('code', $request->district_code)->first();
        $city = City::where('code', $request->city_code)->first();

        if (!$district || !$city) {
            return response()->json(['ok' => false, 'message' => 'Wilayah tidak ditemukan.']);
        }

        if (!$this->provinceAllowed($request->province_code)) {
            return response()->json(['ok' => false, 'message' => 'Pengiriman hanya untuk Jawa Barat dan DKI Jakarta.']);
        }

        $originId = $service->resolveOriginId();

        if (!$originId) {
            return response()->json(['ok' => false, 'message' => 'Lokasi asal toko belum diset (RAJAONGKIR_ORIGIN).']);
        }

        $destId = $service->resolveDistrictId($district->code, $district->name, $city->name);
        $destLabel = $district->name . ', ' . $city->name;

        if (!$destId) {
            return response()->json(['ok' => false, 'message' => 'Tujuan tidak ditemukan di RajaOngkir.']);
        }

        $costs = $service->getCostCached($originId, $destId, $weight, (string) config('rajaongkir.couriers'), 'search');
        $stale = false;

        if ($costs === null || count($costs) === 0) {
            $costs = $service->lastCost($originId, $destId, (string) config('rajaongkir.couriers'));
            $stale = true;
        }

        if ($costs === null || count($costs) === 0) {
            return response()->json(['ok' => false, 'message' => 'Tidak ada layanan pengiriman yang tersedia untuk tujuan ini.']);
        }

        return response()->json([
            'ok' => true,
            'stale' => $stale,
            'weight' => $weight,
            'origin' => (string) config('rajaongkir.origin'),
            'destination' => $destLabel,
            'costs' => array_values($this->normalizeCosts($costs)),
            'cheapest' => $this->cheapest($costs),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'province_code' => ['required', 'string', 'max:2', 'exists:indonesia_provinces,code'],
            'city_code' => ['required', 'string', 'max:4', 'exists:indonesia_cities,code'],
            'district_code' => ['required', 'string', 'max:6', 'exists:indonesia_districts,code'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'note' => ['nullable', 'string', 'max:500'],
            'shipping_cost' => ['nullable', 'integer', 'min:0'],
            'shipping_courier' => ['nullable', 'string', 'max:32'],
            'shipping_service' => ['nullable', 'string', 'max:64'],
            'shipping_estimate' => ['nullable', 'string', 'max:64'],
        ]);

        $cart = $this->cart();

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $cart->load('items.product');

        $province = Province::where('code', $data['province_code'])->first();
        $city = City::where('code', $data['city_code'])->first();
        $district = District::where('code', $data['district_code'])->first();

        if (!$province || !$city || !$district) {
            return back()->withErrors(['address' => 'Wilayah yang dipilih tidak valid.'])->withInput();
        }

if (!$this->provinceAllowed($province->code)) {
            return back()->withErrors(['province_code' => 'Pengiriman hanya untuk Jawa Barat dan DKI Jakarta.'])->withInput();
        }

        foreach ($cart->items as $item) {
            $sizeStock = ProductSize::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->first();

            if (!$sizeStock || (int) $sizeStock->stock < (int) $item->qty) {
                return back()->with('error', 'Maaf, stock "' . $item->size . '" dari ' . $item->product->name
                    . ' hanya tersisa ' . ($sizeStock?->stock ?? 0) . ' unit. Kurangi jumlah pesananmu.');
            }
        }

        [$order, $subtotal, $weight] = DB::transaction(function () use ($cart, $province, $city, $district, $data) {
            $subtotal = 0;
            $weight = 0;

            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_no' => Order::generateInvoiceNo(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'province_code' => $province->code,
                'province_name' => $province->name,
                'city_code' => $city->code,
                'city_name' => $city->name,
                'district_code' => $district->code,
                'district_name' => $district->name,
                'postal_code' => $data['postal_code'] ?? null,
                'weight' => 0,
                'subtotal' => 0,
                'shipping_cost' => 0,
                'total' => 0,
                'note' => $data['note'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $price = (int) round((float) $item->product->price);
                $itemWeight = (int) $item->product->weight;

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'image' => $item->product->image,
                    'size' => $item->size,
                    'qty' => $item->qty,
                    'price' => $price,
                    'weight' => $itemWeight,
                ]);

                $subtotal += $price * $item->qty;
                $weight += $itemWeight * $item->qty;
            }

            $order->allocateStock();

            $cart->items()->delete();

            return [$order, $subtotal, $weight];
        });

        $shipping = $this->verifiedShipping($district, $city, $weight, $data['shipping_courier'] ?? null, $data['shipping_service'] ?? null);

        $order->update([
            'subtotal' => $subtotal,
            'weight' => $weight,
            'shipping_cost' => $shipping['cost'],
            'shipping_courier' => $shipping['courier'],
            'shipping_service' => $shipping['service'],
            'shipping_estimate' => $shipping['estimate'],
            'total' => $subtotal + $shipping['cost'],
        ]);

        if (app(MidtransService::class)->enabled()) {
            $order->load('items');

            try {
                $service = app(MidtransService::class);
                $snap = $service->createTransaction($service->payloadFor($order));

                $order->update([
                    'payment_status' => 'pending',
                    'payment_reference' => $snap['token'] ?? null,
                    'payment_redirect_url' => $snap['redirect_url'] ?? null,
                    'payment_raw' => json_encode($snap),
                ]);

                if (!empty($snap['redirect_url'])) {
                    return redirect()->away($snap['redirect_url']);
                }
            } catch (\Throwable $e) {
                Log::error('Midtrans checkout failed', [
                    'order' => $order->invoice_no,
                    'message' => $e->getMessage(),
                ]);

                return redirect()->route('checkout.success', $order)
                    ->with('error', 'Gateway pembayaran sedang bermasalah. Pesanan tetap tercatat; silakan bayar lewat halaman Contact Us.');
            }
        }

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 404);

        $order->load('items');

        return view('order-success', compact('order'));
    }

    private function cart(): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => auth()->id()],
            ['user_id' => auth()->id()]
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \Laravolt\Indonesia\Models\Province>
     */
    private function allowedProvinces(): \Illuminate\Database\Eloquent\Collection
    {
        $allowed = config('rajaongkir.allowed_provinces', []);

        if (empty($allowed)) {
            return Province::orderBy('name')->get(['code', 'name']);
        }

        return Province::whereIn('code', $allowed)
            ->orderBy('name')
            ->get(['code', 'name']);
    }

    private function provinceAllowed(string $code): bool
    {
        $allowed = config('rajaongkir.allowed_provinces', []);

        return empty($allowed) || in_array($code, $allowed, true);
    }

    /**
     * @param array<int, array<string, mixed>> $costs
     * @return array<int, array<string, mixed>>
     */
    private function normalizeCosts(array $costs): array
    {
        $out = [];

        foreach ($costs as $cost) {
            $out[] = [
                'name' => (string) ($cost['name'] ?? ''),
                'code' => (string) ($cost['code'] ?? ''),
                'service' => (string) ($cost['service'] ?? ''),
                'description' => (string) ($cost['description'] ?? ''),
                'cost' => (int) ($cost['cost'] ?? 0),
                'etd' => (string) ($cost['etd'] ?? ''),
            ];
        }

        usort($out, fn ($a, $b) => $a['cost'] <=> $b['cost']);

        return $out;
    }

    /**
     * @param array<int, array<string, mixed>> $costs
     * @return array<string, mixed>|null
     */
    private function cheapest(array $costs): ?array
    {
        $normalized = $this->normalizeCosts($costs);

        return $normalized[0] ?? null;
    }

    /**
     * Verifikasi ongkir ke server (tidak percaya nilai dari client).
     *
     * @return array{cost: int, courier: string|null, service: string|null, estimate: string|null}
     */
    private function verifiedShipping(District $district, City $city, int $weight, ?string $clientCourier, ?string $clientService): array
    {
        $empty = ['cost' => 0, 'courier' => null, 'service' => null, 'estimate' => null];

        $service = app(RajaOngkirService::class);

        if (!$service->available()) {
            return $empty;
        }

        $originId = $service->resolveOriginId();
        $destId = $service->resolveDistrictId($district->code, $district->name, $city->name);

        if (!$originId || !$destId) {
            return $empty;
        }

        $shippingWeight = max(1, (int) ceil($weight / 1000) * 1000);

        $costs = $service->getCostCached($originId, $destId, $shippingWeight, (string) config('rajaongkir.couriers'), 'search');

        if ($costs === null || count($costs) === 0) {
            $costs = $service->lastCost($originId, $destId, (string) config('rajaongkir.couriers'));
        }

        if ($costs === null || count($costs) === 0) {
            return $empty;
        }

        $match = collect($costs)->first(function ($c) use ($clientCourier, $clientService) {
            return $clientCourier && $clientService
                && strtolower((string) $c['code']) === strtolower((string) $clientCourier)
                && strtolower((string) $c['service']) === strtolower((string) $clientService);
        });

        if (!$match) {
            $match = $this->cheapest($costs) ?? [];
        }

        return [
            'cost' => (int) ($match['cost'] ?? 0),
            'courier' => ($match['code'] ?? null) ? (string) $match['code'] : null,
            'service' => ($match['service'] ?? null) ? (string) $match['service'] : null,
            'estimate' => ($match['etd'] ?? null) ? (string) $match['etd'] : null,
        ];
    }
}