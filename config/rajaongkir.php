<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir Configuration
    |--------------------------------------------------------------------------
    |
    | Integrasi RajaOngkir API V2 (rajaongkir.komerce.id/api/v1).
    | Aktif sampai RAJAONGKIR_ENABLED=true dan RAJAONGKIR_API_KEY diisi.
    |
    */

    'enabled' => env('RAJAONGKIR_ENABLED', false),

    'api_key' => env('RAJAONGKIR_API_KEY', ''),

    'account_type' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter'),

    'base_url' => env('RAJAONGKIR_BASE_URL') ?: 'https://rajaongkir.komerce.id/api/v1',

    'origin' => env('RAJAONGKIR_ORIGIN', ''),

    /*
    | Ekspedisi yang ditawarkan (kode dipisah titik dua).
    */
    'couriers' => env('RAJAONGKIR_COURIERS', 'jne:jnt'),

    /*
    | Durasi cache hasil harga ongkir (detik). Default 2 hari.
    | Jika kuota habis / API error, transaksi memakai harga terakhir yang tersimpan.
    */
    'cost_cache_ttl' => (int) env('RAJAONGKIR_COST_TTL', 172800),

    /*
    | Provinsi tujuan yang diizinkan (kode dipisah koma).
    | Kosongkan untuk mengizinkan semua provinsi.
    */
    'allowed_provinces' => array_values(array_filter(array_map('trim', explode(',', (string) env('RAJAONGKIR_PROVINCES', ''))))),

    'price_surcharge' => 5000,
];