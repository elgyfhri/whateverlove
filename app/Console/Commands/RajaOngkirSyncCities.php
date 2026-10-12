<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class RajaOngkirSyncCities extends Command
{
    protected $signature = 'rajaongkir:sync';

    protected $description = 'Sinkronisasi ID kota RajaOngkir ke tabel indonesia_cities (rajaongkir_id)';

    public function handle(RajaOngkirService $service): int
    {
        if (!config('rajaongkir.api_key')) {
            $this->error('RAJAONGKIR_API_KEY belum diisi di .env');
            return self::FAILURE;
        }

        try {
            $updated = $service->syncCities();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Sinkronisasi selesai. Kota yang dicocokkan: {$updated}");

        return self::SUCCESS;
    }
}