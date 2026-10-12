<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Estimations
    |--------------------------------------------------------------------------
    |
    | eta_buffer_days: hari cadangan yang ditambahkan ke ETD kurir (JNE dll)
    | supaya paket Diharapkan sampai lebih cepat daripada prediksi, bukan telat.
    |
    */
    'eta_buffer_days' => (int) env('SHIPPING_ETA_BUFFER_DAYS', 2),
];