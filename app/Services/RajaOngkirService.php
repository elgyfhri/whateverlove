<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;

class RajaOngkirService
{
    public function available(): bool
    {
        return config('rajaongkir.enabled')
            && config('rajaongkir.api_key') !== '';
    }

    public function baseUrl(): string
    {
        return rtrim((string) config('rajaongkir.base_url'), '/');
    }

    public function headers(): array
    {
        return [
            'key' => (string) config('rajaongkir.api_key'),
            'Accept' => 'application/json',
        ];
    }

    private function http(): PendingRequest
    {
        $req = Http::withHeaders($this->headers())->acceptJson();

        $ca = $this->caBundle();
        if ($ca !== null) {
            $req = $req->withOptions(['verify' => $ca]);
        }

        return $req;
    }

    private function caBundle(): ?string
    {
        $candidates = [
            getenv('RAJAONGKIR_CA_BUNDLE') ?: null,
            'C:\xampp\phpMyAdmin\vendor\composer\ca-bundle\res\cacert.pem',
            'C:\xampp\perl\vendor\lib\Mozilla\CA\cacert.pem',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Cek koneksi + validasi API key lewat endpoint daftar provinsi.
     *
     * @return array{ok: bool, message: string}
     */
    public function check(): array
    {
        if (!config('rajaongkir.api_key')) {
            return ['ok' => false, 'message' => 'RAJAONGKIR_API_KEY belum diisi di .env'];
        }

        try {
            $res = $this->http()
                ->timeout(20)
                ->get($this->baseUrl() . '/destination/province');
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage()];
        }

        $json = $res->json();
        $status = (string) data_get($json, 'meta.status', '');
        $message = (string) data_get($json, 'meta.message', '');
        $count = count((array) data_get($json, 'data', []));

        if ($res->ok() && $status === 'success') {
            return ['ok' => true, 'message' => 'Koneksi OK. Data provinsi: ' . $count];
        }

        if ($status !== '' && $message !== '') {
            return ['ok' => false, 'message' => 'HTTP ' . $res->status() . ' — ' . $message];
        }

        return ['ok' => false, 'message' => 'HTTP ' . $res->status() . ' — ' . $res->body()];
    }

    /**
     * Cari tujuan domestik (search endpoint mengembalikan ID subdistrict).
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchDestination(string $query, int $limit = 5): array
    {
        if (!$this->available()) {
            return [];
        }

        try {
            $res = $this->http()
                ->timeout(20)
                ->get($this->baseUrl() . '/destination/domestic-destination', [
                    'search' => $query,
                    'limit' => $limit,
                    'offset' => 0,
                ]);
        } catch (\Throwable $e) {
            return [];
        }

        if (!$res->ok()) {
            return [];
        }

        return (array) data_get($res->json(), 'data', []);
    }

    /**
     * Resolve lokasi ke ID RajaOngkir via search.
     * Prioritas: cocok field kota/kecamatan (normalisasi KABUPATEN/KOTA) → semua token di label → token paling spesifik.
     */
    public function resolveDestinationId(string $query, ?string $cityName = null, ?string $districtName = null): ?int
    {
        if (trim($query) === '' || !$this->available()) {
            return null;
        }

        $rows = $this->searchDestination($query, 10);

        if (empty($rows)) {
            return null;
        }

        $normalize = fn (string $s): string => strtoupper(trim(preg_replace('/^(KOTA|KABUPATEN)\s+/', '', trim($s)) ?? $s));

        $qCity = $cityName !== null ? $normalize($cityName) : '';
        $qDist = $districtName !== null ? $normalize($districtName) : '';

        foreach ($rows as $row) {
            $rCity = $normalize((string) ($row['city_name'] ?? ''));
            $rDist = $normalize((string) ($row['district_name'] ?? ''));

            if ($qCity !== '' && $rCity === $qCity && ($qDist === '' || $rDist === $qDist)) {
                return (int) $row['id'];
            }
        }

        $terms = array_values(array_filter(
            array_map('strtoupper', preg_split('/[\s,]+/', trim($query))),
            fn ($t) => mb_strlen($t) >= 3
        ));

        foreach ($rows as $row) {
            $label = strtoupper((string) ($row['label'] ?? ''));
            $all = count($terms) > 0 && collect($terms)->every(fn ($t) => str_contains($label, $t));

            if ($all) {
                return (int) $row['id'];
            }
        }

        if (count($terms) > 0) {
            $last = (string) end($terms);

            foreach ($rows as $row) {
                $rCity = strtoupper((string) ($row['city_name'] ?? ''));
                $rDist = strtoupper((string) ($row['district_name'] ?? ''));

                if ($rCity === $last || $rDist === $last) {
                    return (int) $row['id'];
                }
            }
        }

        return (int) $rows[0]['id'];
    }

    /**
     * Hitung ongkos kirim domestik.
     *
     * @param string|int $originId      ID asal  (subdistrict ID dari search / district ID dari cascading)
     * @param string|int $destinationId ID tujuan (subdistrict ID dari search / district ID dari cascading)
     * @param string $endpoint          'search'  → /calculate/domestic-cost (subdistrict IDs)
     *                                  'district' → /calculate/district/domestic-cost (district/subdistrict IDs)
     * @param string $courier           Colon-separated courier codes, contoh 'jne:jnt:sicepat'
     * @return array<int, array<string, mixed>> daftar layanan: name, code, service, description, cost, etd
     */
    public function getCost(
        string|int $originId,
        string|int $destinationId,
        int $weightGram,
        string $courier = 'jne:jnt:sicepat:tiki:pos:ninja',
        string $endpoint = 'search',
        string $price = 'lowest',
    ): ?array {
        if (!$this->available()) {
            return null;
        }

        $path = $endpoint === 'district'
            ? '/calculate/district/domestic-cost'
            : '/calculate/domestic-cost';

        try {
            $res = $this->http()
                ->timeout(30)
                ->asForm()
                ->post($this->baseUrl() . $path, [
                    'origin' => $originId,
                    'destination' => $destinationId,
                    'weight' => max(1, $weightGram),
                    'courier' => $courier,
                    'price' => $price,
                ]);
        } catch (\Throwable $e) {
            return null;
        }

        $json = $res->json();

        if (!$res->ok() || (string) data_get($json, 'meta.status', '') !== 'success') {
            return null;
        }

        return (array) data_get($json, 'data', []);
    }

    /**
     * ID asal toko (RAJAONGKIR_ORIGIN) — dicari sekali, di-cache 30 hari.
     */
    public function resolveOriginId(): ?int
    {
        $origin = (string) config('rajaongkir.origin');

        if (trim($origin) === '') {
            return null;
        }

        return cache()->remember('rajaongkir.origin.' . md5($origin), 60 * 60 * 24 * 30, function () use ($origin) {
            return $this->resolveDestinationId($origin);
        });
    }

    /**
     * ID RajaOngkir untuk kecamatan tertentu — dicari sekali, disimpan di indonesia_districts.rajaongkir_id.
     */
    public function resolveDistrictId(string $districtCode, string $districtName, string $cityName): ?int
    {
        $district = District::where('code', $districtCode)->first();

        if ($district && $district->rajaongkir_id) {
            return (int) $district->rajaongkir_id;
        }

        $id = $this->resolveDestinationId(trim($districtName . ' ' . $cityName), $cityName, $districtName);

        if ($id && $district) {
            $district->update(['rajaongkir_id' => $id]);
        }

        return $id;
    }

    /**
     * getCost dengan cache supaya kuota hit hemat.
     *
     * Selalu menyimpan hasil sukses sebagai “harga terakhir” (30 hari) sebagai cadangan
     * bila nanti kuota habis / API error. Gagal sementara tidak ikut di-cache.
     *
     * @return array<int, array<string, mixed>>|null
     */
    public function getCostCached(
        string|int $originId,
        string|int $destinationId,
        int $weightGram,
        string $courier = 'jne:jnt:sicepat:tiki:pos:ninja',
        string $endpoint = 'search',
        string $price = 'lowest',
        ?int $ttl = null,
    ): ?array {
        $key = $this->costCacheKey($originId, $destinationId, $weightGram, $courier, $endpoint, $price);
        $cached = cache()->get($key);

        if ($cached !== null) {
            return (array) $cached;
        }

        $costs = $this->getCost(
            $originId,
            $destinationId,
            max(1, $weightGram),
            $courier,
            $endpoint,
            $price
        );

        if ($costs !== null && count($costs) > 0) {
            $ttl ??= (int) config('rajaongkir.cost_cache_ttl', 172800);

            cache()->put($key, $costs, $ttl);
            cache()->put($this->lastCostKey($originId, $destinationId, $courier), $costs, 60 * 60 * 24 * 30);
        }

        return $costs;
    }

    /**
     * Harga terakhir yang berhasil diambil untuk tujuan + kurir tertentu (cadangan kuota habis).
     *
     * @return array<int, array<string, mixed>>|null
     */
    public function lastCost(
        string|int $originId,
        string|int $destinationId,
        string $courier,
    ): ?array {
        $last = cache()->get($this->lastCostKey($originId, $destinationId, $courier));

        return is_array($last) && count($last) > 0 ? $last : null;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function costCacheKey(
        string|int $originId,
        string|int $destinationId,
        int $weightGram,
        string $courier,
        string $endpoint,
        string $price,
    ): string {
        return 'rajaongkir.cost.' . md5(implode('|', [
            $endpoint,
            $originId,
            $destinationId,
            max(1, $weightGram),
            $courier,
            $price,
        ]));
    }

    private function lastCostKey(string|int $originId, string|int $destinationId, string $courier): string
    {
        return 'rajaongkir.last.' . md5(implode('|', [$originId, $destinationId, $courier]));
    }

    /**
     * Sinkronisasi rajaongkir_id ke indonesia_cities berdasarkan nama provinsi + kota.
     *
     * @return int jumlah kota yang berhasil dicocokkan
     */
    public function syncCities(): int
    {
        try {
            $provincesRes = $this->http()->timeout(30)->get($this->baseUrl() . '/destination/province');
        } catch (\Throwable $e) {
            throw new \RuntimeException('Koneksi gagal: ' . $e->getMessage());
        }

        if (!$provincesRes->ok()) {
            throw new \RuntimeException('HTTP ' . $provincesRes->status() . ' — ' . $provincesRes->body());
        }

        if ((string) data_get($provincesRes->json(), 'meta.status', '') !== 'success') {
            throw new \RuntimeException((string) data_get($provincesRes->json(), 'meta.message', 'Gagal memuat provinsi'));
        }

        $ourProvinces = \Laravolt\Indonesia\Models\Province::orderBy('name')->get(['code', 'name']);
        $updated = 0;

        foreach (data_get($provincesRes->json(), 'data', []) as $rkProvince) {
            $rkName = strtoupper(trim((string) ($rkProvince['name'] ?? '')));
            $ourProvince = $ourProvinces->first(fn ($p) => strtoupper(trim($p->name)) === $rkName);

            if (!$ourProvince) {
                continue;
            }

            try {
                $citiesRes = $this->http()->timeout(30)->get($this->baseUrl() . '/destination/city/' . $rkProvince['id']);
            } catch (\Throwable $e) {
                continue;
            }

            if (!$citiesRes->ok()) {
                continue;
            }

            foreach ((array) data_get($citiesRes->json(), 'data', []) as $rkCity) {
                $rkCityName = strtoupper(trim((string) ($rkCity['name'] ?? '')));

                $match = City::where('province_code', $ourProvince->code)
                    ->whereRaw('UPPER(TRIM(name)) = ?', [$rkCityName])
                    ->first();

                if ($match && $match->rajaongkir_id !== (int) $rkCity['id']) {
                    $match->update(['rajaongkir_id' => (int) $rkCity['id']]);
                    $updated++;
                }
            }
        }

        return $updated;
    }
}