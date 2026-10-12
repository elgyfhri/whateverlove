<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans
    |--------------------------------------------------------------------------
    |
    | Ambil Server Key & Client Key dari dashboard.sandbox.midtrans.com
    | (Settings -> Access Keys). Saat siap produksi, ganti is_production=true
    | dan gunakan key produksi dari dashboard.midtrans.com.
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    // Masa berlaku transaksi Snap sebelum dinyatakan expired
    'expiry_hours' => (int) env('MIDTRANS_EXPIRY_HOURS', 24),
    // Path CA bundle untuk mengatasi "SSL certificate problem" di Windows/XAMPP
    'ca_bundle' => env('MIDTRANS_CA_BUNDLE', env('RAJAONGKIR_CA_BUNDLE', '')),
    // Metode pembayaran yang diizinkan di halaman Snap (kosong = semua).
    // Contoh: "credit_card,qris,bank_transfer"
    'enabled_payments' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('MIDTRANS_ENABLED_PAYMENTS', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Kode metode pembayaran
    |--------------------------------------------------------------------------
    |
    | Daftar kode yang valid untuk parameter `enabled_payments` milik Snap.
    | Nilai di luar daftar ini akan ditolak Midtrans dengan HTTP 400, jadi
    | `enabled_payments` di-allowlist di sini sebelum dikirim.
    |
    | Referensi: docs.midtrans.com/docs/snap-advanced-feature
    |
    */
    'payment_types' => [
        // Kartu kredit / debit
        'credit_card',

        // E-wallet
        'gopay',
        'shopeepay',
        'dana',
        'ovo',
        'linkaja',

        // QRIS (semua e-money Indonesia)
        'qris',

        // Virtual account & bank transfer
        'bank_transfer',
        'bca_va',
        'bni_va',
        'bri_va',
        'permata_va',
        'other_va',
        'echannel',
        'cimb_va',
        'bsi_va',
        'danamon_va',
        'seabank_va',
        'saqu_va',

        // Retail outlet (over the counter)
        'retail_outlet',
        'Indomaret',
        'alfamart',
        'kioson',

        // Cardless credit (BNPL)
        'akulaku',
        'kredivo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Alias
    |--------------------------------------------------------------------------
    |
    | Alias `enabled_payments` yang 전개 ke beberapa payment type sekaligus.
    | Source: docs.midtrans.com/docs/snap-advanced-feature
    |
    */
    'payment_type_aliases' => [
        'bank_transfer' => ['permata_va', 'bca_va', 'bni_va', 'bri_va', 'echannel'],
        'store' => ['kioson', 'indomaret', 'alfamart'],
        'retail_outlet' => ['kioson', 'indomaret', 'alfamart'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Label metode pembayaran (untuk ditampilkan di halaman order)
    |--------------------------------------------------------------------------
    */
    'payment_type_labels' => [
        'credit_card' => 'Kartu Kredit / Debit',
        'gopay' => 'GoPay',
        'shopeepay' => 'ShopeePay',
        'dana' => 'DANA',
        'ovo' => 'OVO',
        'linkaja' => 'LinkAja',
        'qris' => 'QRIS',
        'bank_transfer' => 'Virtual Account',
        'bca_va' => 'Virtual Account BCA',
        'bni_va' => 'Virtual Account BNI',
        'bri_va' => 'Virtual Account BRI',
        'permata_va' => 'Virtual Account Permata',
        'other_va' => 'Virtual Account Lainnya',
        'cimb_va' => 'Virtual Account CIMB',
        'bsi_va' => 'Virtual Account BSI',
        'danamon_va' => 'Virtual Account Danamon',
        'seabank_va' => 'Virtual Account SeaBank',
        'saqu_va' => 'Virtual Account Saqu',
        'echannel' => 'Internet Banking / Mobile Banking',
        'retail_outlet' => 'Gerai Ritel',
        'Indomaret' => 'Gerai Indomaret',
        'alfamart' => 'Gerai Alfamart',
        'kioson' => 'Gerai Kioson',
        'akulaku' => 'Akulaku',
        'kredivo' => 'Kredivo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Metode yang butuh instruksi pembayaran manual
    |--------------------------------------------------------------------------
    |
    | Untuk payment type di bawah ini, Snap hanya menampilkan nomor VA / kode
    | pembayaran. Nomor itu wajib ditampilkan di halaman order supaya pelanggan
    | tahu cara menyelesaikan pembayaran.
    |
    */
    'manual_instruction_types' => [
        'bank_transfer',
        'bca_va',
        'bni_va',
        'bri_va',
        'permata_va',
        'other_va',
        'cimb_va',
        'bsi_va',
        'danamon_va',
        'seabank_va',
        'saqu_va',
        'echannel',
        'retail_outlet',
        'Indomaret',
        'alfamart',
        'kioson',
    ],
];
