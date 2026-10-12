<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Payment type yang butuh nomor VA / kode bayar ditampilkan manual.
     */
    private const MANUAL_INSTRUCTION_TYPES = [
        'bank_transfer', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
        'cimb_va', 'bsi_va', 'danamon_va', 'seabank_va', 'saqu_va', 'echannel',
        'retail_outlet', 'Indomaret', 'alfamart', 'kioson',
    ];

    public function enabled(): bool
    {
        $key = config('midtrans.server_key');

        return is_string($key) && $key !== '';
    }

    public function isProduction(): bool
    {
        return (bool) config('midtrans.is_production', false);
    }

    public function snapBase(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    public function apiBase(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Basic '.base64_encode(config('midtrans.server_key').':')];
    }

    private function clientOptions(): array
    {
        $bundle = (string) config('midtrans.ca_bundle');

        return $bundle !== '' ? ['verify' => $bundle] : [];
    }

    /**
     * Buat transaksi Snap. Mengembalikan { token, redirect_url, ... } dari Midtrans.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createTransaction(array $payload): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->withOptions($this->clientOptions())
            ->acceptJson()
            ->post($this->snapBase().'/snap/v1/transactions', $payload);

        $body = $response->json() ?? [];

        if ($response->failed()) {
            Log::error('Midtrans createTransaction failed', [
                'status' => $response->status(),
                'body' => $body,
            ]);
            throw new \RuntimeException(
                'Gagal membuat transaksi pembayaran: '.($body['status_message'] ?? 'Penyedia pembayaran tidak merespon.')
            );
        }

        return $body;
    }

    /**
     * Ambil status transaksi terbaru dari Midtrans.
     *
     * @return array<string, mixed>
     */
    public function getStatus(string $orderId): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->withOptions($this->clientOptions())
            ->acceptJson()
            ->get($this->apiBase().'/v2/'.urlencode($orderId).'/status');

        return $response->json() ?? [];
    }

    /**
     * Batalkan transaksi yang masih pending (supaya order_id bisa dipakai transaksi baru).
     *
     * @return array<string, mixed>
     */
    public function cancelTransaction(string $orderId): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->withOptions($this->clientOptions())
            ->acceptJson()
            ->post($this->apiBase().'/v2/'.urlencode($orderId).'/cancel');

        return $response->json() ?? [];
    }

    /**
     * Rawat kode `enabled_payments`: buang nilai yang tidak dikenal lalu
     * kembalikan daftar yang aman dikirim (alias ikut di-expand).
     *
     * Midtrans menolak seluruh request dengan HTTP 400 kalau ada satu saja kode
     * yang tidak valid, jadi allowlist di sini wajib dipakai.
     *
     * @return array<int, string>
     */
    public function sanitizePaymentTypes(): array
    {
        $allowed = (array) config('midtrans.payment_types', []);
        $aliases = (array) config('midtrans.payment_type_aliases', []);

        $requested = config('midtrans.enabled_payments', []);

        // Kosong = tidak mengirim enabled_payments = Snap menampilkan semua
        // metode yang sudah diaktifkan di dashboard merchant.
        if (empty($requested) || ! is_array($requested)) {
            return [];
        }

        $out = [];

        foreach ($requested as $type) {
            $type = (string) $type;

            if (isset($aliases[$type])) {
                foreach ($aliases[$type] as $expanded) {
                    // Alias ditulis lowercase di docs, tapi response Midtrans
                    // memakai kapitalisasi campur (mis. "Indomaret").
                    $match = $this->matchPaymentType($expanded, $allowed);

                    if ($match !== null && ! in_array($match, $out, true)) {
                        $out[] = $match;
                    }
                }

                continue;
            }

            $match = $this->matchPaymentType($type, $allowed);

            if ($match === null) {
                Log::warning('Midtrans enabled_payments contains unknown type, ignored', [
                    'type' => $type,
                ]);

                continue;
            }

            if (! in_array($match, $out, true)) {
                $out[] = $match;
            }
        }

        return $out;
    }

    /**
     * @param  array<int, string>  $allowed
     */
    private function matchPaymentType(string $type, array $allowed): ?string
    {
        foreach ($allowed as $candidate) {
            if (strcasecmp($candidate, $type) === 0) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Bangun payload transaksi Snap dari sebuah order.
     *
     * `gross_amount` sengaja dihitung dari penjumlahan `item_details`, bukan
     * dari `order->total`. Midtrans memvalidasi keduanya harus sama persis,
     * dan pembulatan decimal bisa membuat selisih 1-2 rupiah.
     *
     * @return array<string, mixed>
     */
    public function payloadFor(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => 'P'.$item->product_id,
                'price' => (int) round((float) $item->price),
                'quantity' => (int) $item->qty,
                'name' => mb_substr($item->product_name, 0, 50),
            ];
        }

        $shipping = (int) round((float) $order->shipping_cost);

        if ($shipping > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => $shipping,
                'quantity' => 1,
                'name' => mb_substr(
                    'Ongkos kirim '.($order->shipping_courier ? $order->shipping_courier.' ' : '').$order->shipping_service,
                    0,
                    50
                ),
            ];
        }

        $gross = 0;

        foreach ($items as $item) {
            $gross += $item['price'] * $item['quantity'];
        }

        if ($gross < 1) {
            throw new \RuntimeException('Total pembayaran tidak valid, transaksi tidak dapat dibuat.');
        }

        $address = [
            'first_name' => mb_substr($order->name, 0, 50),
            'phone' => (string) $order->phone,
            'address' => mb_substr($order->address, 0, 200),
            'city' => $order->city_name,
            'postal_code' => (string) ($order->postal_code ?: ''),
            'country_code' => 'IDN',
        ];

        $payload = [
            'transaction_details' => [
                'order_id' => $order->invoice_no,
                'gross_amount' => $gross,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => mb_substr($order->name, 0, 50),
                'email' => $order->email,
                'phone' => (string) $order->phone,
                'billing_address' => $address,
                'shipping_address' => $address,
            ],
            'expiry' => [
                // Midtrans menafsirkan start_time sebagai waktu Indonesia Barat (WIT).
                'start_time' => now('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                'unit' => 'hours',
                'duration' => (int) config('midtrans.expiry_hours', 24),
            ],
            'callbacks' => [
                'finish' => route('payment.return', $order),
            ],
        ];

        $enabledPayments = $this->sanitizePaymentTypes();

        if (! empty($enabledPayments)) {
            $payload['enabled_payments'] = $enabledPayments;
        }

        // 3DS wajib aktif untuk kartu kredit agar aman dari chargeback.
        if (in_array('credit_card', $enabledPayments ?: (array) config('midtrans.payment_types', []), true)) {
            $payload['credit_card'] = ['secure' => true];
        }

        return $payload;
    }

    /**
     * Cocokkan transaction_status Midtrans -> payment_status aplikasi.
     */
    public function mapPaymentStatus(?string $transactionStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement', 'success' => 'paid',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            'deny', 'failure' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Verifikasi signature_key dari notifikasi Midtrans.
     *
     * Tanpa ini, siapa pun yang tahu nomor invoice bisa memalsukan webhook
     * untuk menandai order sebagai lunas.
     *
     * @param  array<string, mixed>  $data
     */
    public function verifySignature(array $data): bool
    {
        $orderId = (string) ($data['order_id'] ?? '');
        $statusCode = (string) ($data['status_code'] ?? '');
        $grossAmount = (string) ($data['gross_amount'] ?? '');
        $signature = (string) ($data['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $signature === '') {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.(string) config('midtrans.server_key'));

        return hash_equals($expected, $signature);
    }

    /**
     * Terapkan status pembayaran dari notifikasi/query ke order.
     *
     * @param  array<string, mixed>  $data
     */
    public function applyStatus(Order $order, array $data): bool
    {
        $status = $this->mapPaymentStatus($data['transaction_status'] ?? null);

        // Pembayaran yang sudah lunas tidak boleh turun kembali menjadi
        // pending/expired/cancelled, karena notifikasi bisa arrive tidak berurutan.
        if ($order->payment_status === 'paid' && $status !== 'paid') {
            return false;
        }

        $statusChanged = $order->payment_status !== $status;

        $update = [];

        if ($statusChanged) {
            $update['payment_status'] = $status;
            $update['payment_reference'] = $data['transaction_id'] ?? $data['payment_type'] ?? $order->payment_reference;

            if ($status === 'paid') {
                $update['paid_at'] = $data['settlement_time'] ?? now();

                if ($order->status === 'pending') {
                    $update['status'] = 'processing';
                }
            }
        }

        if (! empty($data)) {
            $merged = is_string($order->payment_raw)
                ? array_merge(json_decode($order->payment_raw, true) ?? [], $data)
                : $data;

            // Payment type memakai key yang sama (mis. bca_va vs bni_va) sehingga
            // merge berulang akan menumpuk nilai basi. Simpan hanya data terakhir.
            $slim = $this->slimPaymentRaw($merged);
            $encoded = json_encode($slim);

            // Nomor VA / kode bayar baru muncul di notifikasi berstatus
            // "pending", jadi payload tetap disimpan walau status tidak berubah
            // supaya nomor tersebut bisa ditampilkan ke pelanggan.
            if ($encoded !== false && $encoded !== $order->payment_raw) {
                $update['payment_raw'] = $encoded;
            }
        }

        if (empty($update)) {
            return false;
        }

        $order->update($update);

        if ($statusChanged && in_array($status, ['expired', 'cancelled'], true)) {
            $order->releaseStock();
        }

        if ($statusChanged) {
            Log::info('Midtrans order status updated', [
                'order' => $order->invoice_no,
                'status' => $status,
            ]);
        }

        return true;
    }

    /**
     * Buang field yang tidak diperlukan supaya payment_raw tidak tumbuh tanpa batas.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function slimPaymentRaw(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'transaction_time',
            'transaction_status',
            'transaction_id',
            'status_code',
            'status_message',
            'payment_type',
            'order_id',
            'gross_amount',
            'settlement_time',
            'fraud_status',
            'store',
            'bank',
            'va_numbers',
            'permata_va_number',
            'payment_code',
            'bill_key',
            'biller_code',
            'masked_card',
            'card_type',
            'channel',
            'issuer',
            'acquirer',
            'qris_code',
        ]));
    }

    /**
     * Nomor VA / kode bayar dari payload Midtrans, untuk ditampilkan ke pelanggan.
     *
     * @param  array<string, mixed>  $data
     * @return array{type: string, label: string, number: string|null, expires_at: string|null}|null
     */
    public function paymentInstruction(?array $data): ?array
    {
        if (! $data) {
            return null;
        }

        $type = (string) ($data['payment_type'] ?? '');

        $number = null;
        $channel = null;

        if (! empty($data['va_numbers']) && is_array($data['va_numbers'])) {
            $va = collect($data['va_numbers'])->first();
            $number = is_array($va) ? ($va['va_number'] ?? null) : $va;
            $channel = is_array($va) ? ($va['bank'] ?? null) : null;
        } elseif (! empty($data['permata_va_number'])) {
            $number = (string) $data['permata_va_number'];
        } elseif (! empty($data['payment_code'])) {
            $number = (string) $data['payment_code'];
        } elseif (! empty($data['qris_code'])) {
            $number = is_array($data['qris_code'])
                ? ($data['qris_code']['qr_string'] ?? null)
                : $data['qris_code'];
        } elseif (! empty($data['bill_key']) && ! empty($data['biller_code'])) {
            $number = $data['biller_code'].' '.$data['bill_key'];
        }

        if (! $number) {
            return null;
        }

        if ($channel) {
            $type = $type === 'bank_transfer' && $channel ? $channel.'_va' : $type;
        }

        $labels = (array) config('midtrans.payment_type_labels', []);

        return [
            'type' => $type,
            'label' => $labels[$type] ?? ($data['store'] ?? null ? ucfirst((string) $data['store']) : 'Informasi Pembayaran'),
            'number' => (string) $number,
            'expires_at' => $data['transaction_time'] ?? null,
        ];
    }

    /**
     * Apakah payment type ini butuh pelanggan untuk_transfer manual?
     */
    public function needsManualInstruction(?string $paymentType): bool
    {
        if (! $paymentType) {
            return false;
        }

        return in_array($paymentType, self::MANUAL_INSTRUCTION_TYPES, true)
            || in_array($paymentType, (array) config('midtrans.manual_instruction_types', []), true);
    }

    /**
     * Sinkronkan status order langsung ke Midtrans (dipakai di halaman "Cek Status").
     */
    public function syncFromMidtrans(Order $order): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        $data = $this->getStatus($order->invoice_no);

        if (empty($data) || ($data['status_code'] ?? null) === '404') {
            return false;
        }

        return $this->applyStatus($order, $data);
    }
}
