<?php

namespace App\Models;

use App\Services\MidtransService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const DAY_NAMES = [
        'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
    ];

    public const MONTH_NAMES = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    protected $fillable = [
        'user_id',
        'invoice_no',
        'name',
        'email',
        'phone',
        'address',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
        'district_code',
        'district_name',
        'postal_code',
        'weight',
        'subtotal',
        'shipping_cost',
        'shipping_courier',
        'shipping_service',
        'shipping_estimate',
        'total',
        'note',
        'status',
        'payment_status',
        'payment_reference',
        'payment_redirect_url',
        'payment_raw',
        'paid_at',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'stock_released',
    ];

    protected $casts = [
        'weight' => 'integer',
        'subtotal' => 'integer',
        'shipping_cost' => 'integer',
        'total' => 'integer',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'stock_released' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateInvoiceNo(): string
    {
        return 'WL-'.date('Ymd').'-'.strtoupper(Str::random(6));
    }

    /**
     * Label estimasi pengiriman. ETD dihitung sejak PAKET DIKIRIM (shipped_at)
     * dan sudah termasuk hari cadangan (config shipping.eta_buffer_days),
     * jadi paket cenderung tiba lebih cepat daripada prediksi.
     */
    public function shippingEtaLabel(): string
    {
        $etd = trim((string) $this->shipping_estimate);

        if ($etd === '') {
            return 'dihitung setelah paket dikirim';
        }

        $days = $this->etdDays($etd) ?? 0;
        $total = $days + (int) config('shipping.eta_buffer_days', 2);

        $base = $this->shipped_at;
        if ($base === null) {
            return '± '.$total.' hari';
        }

        $date = $base->copy()->addDays($total);

        return '± '.static::DAY_NAMES[(int) $date->dayOfWeek].', '
            .$date->day.' '.static::MONTH_NAMES[$date->month - 1].' '.$date->year
            .' ('.$total.' hari)';
    }

    /**
     * Ambil jumlah hari maksimal dari string ETD (mis. "1-2 hari", "3 day", "2 hari kerja").
     */
    public function etdDays(string $etd): ?int
    {
        if (preg_match('/\b(\d+)\s*(?:-|–|\x{2013}|sampai)\s*(\d+)\s*(?:hari|day|days)\b/iu', $etd, $m)) {
            return (int) $m[2];
        }

        if (preg_match('/\b(\d+)\s*(?:hari|day|days)\b/i', $etd, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    public function paymentStatusLabel(): string
    {
        return [
            'paid' => 'Pembayaran Dikonfirmasi',
            'expired' => 'Kedaluwarsa',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan',
        ][$this->payment_status] ?? ($this->payment_status ? ucfirst($this->payment_status) : 'Menunggu Pembayaran');
    }

    /**
     * Data pembayaran terakhir dari Midtrans dalam bentuk array.
     *
     * @return array<string, mixed>
     */
    public function paymentData(): array
    {
        if (! is_string($this->payment_raw) || $this->payment_raw === '') {
            return [];
        }

        $decoded = json_decode($this->payment_raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Nomor VA / kode bayar yang harus ditampilkan ke pelanggan, atau null
     * kalau metode pembayaran tidak butuh instruksi manual.
     *
     * @return array{type: string, label: string, number: string|null, expires_at: string|null}|null
     */
    public function paymentInstruction(): ?array
    {
        return app(MidtransService::class)->paymentInstruction($this->paymentData());
    }

    /**
     * Label metode pembayaran yang dipakai pelanggan (mis. "GoPay", "QRIS").
     */
    public function paymentTypeLabel(): ?string
    {
        $data = $this->paymentData();
        $type = $data['payment_type'] ?? null;

        if (! $type) {
            return null;
        }

        $labels = (array) config('midtrans.payment_type_labels', []);

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', (string) $type));
    }

    public function statusLabel(): string
    {
        return [
            'pending' => 'Menunggu',
            'processing' => 'Diproses / Dikemas',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'cancelled' => 'Dibatalkan',
        ][$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Cek apakah seluruh item order masih tersedia di stock (per size).
     */
    public function stockAvailable(): bool
    {
        $this->loadMissing('items');

        foreach ($this->items as $item) {
            $size = ProductSize::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->first();

            if (! $size || (int) $size->stock < (int) $item->qty) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kurangi stock per size sesuai item order (dipanggil saat pesanan dibuat).
     */
    public function allocateStock(): void
    {
        $this->loadMissing('items');

        foreach ($this->items as $item) {
            ProductSize::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->decrement('stock', (int) $item->qty);
        }
    }

    /**
     * Kembalikan stock (dipanggil saat order dibatalkan / pembayaran kedaluwarsa).
     * Hanya sekali: dijaga oleh kolom stock_released.
     */
    public function releaseStock(): void
    {
        if ($this->stock_released) {
            return;
        }

        $this->loadMissing('items');

        foreach ($this->items as $item) {
            ProductSize::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->increment('stock', (int) $item->qty);
        }

        $this->stock_released = true;
        $this->save();
    }
}
