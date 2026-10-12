<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class RajaOngkirCheck extends Command
{
    protected $signature = 'rajaongkir:check';

    protected $description = 'Tes koneksi RajaOngkir dengan API key yang ada di .env';

    public function handle(RajaOngkirService $service): int
    {
        $result = $service->check();

        $this->line($result['ok']
            ? "<info>[OK]</info> {$result['message']}"
            : "<error>[FAIL]</error> {$result['message']}");

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}