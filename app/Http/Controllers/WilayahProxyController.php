<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class WilayahProxyController extends Controller
{
    private function fetchJson(string $path): JsonResponse
    {
        $base = rtrim((string) config('wilayah.base_url'), '/');
        $url = $base.'/'.$path;

        $response = Http::timeout(20)
            ->acceptJson()
            ->get($url);

        if (! $response->successful()) {
            return response()->json([
                'message' => __('Gagal memuat data wilayah. Coba lagi nanti.'),
            ], 502);
        }

        // Some upstream responses may include a BOM or stray bytes that break `res.json()` in browsers.
        $body = (string) $response->body();
        $body = preg_replace('/^\xEF\xBB\xBF/', '', $body) ?? $body; // UTF-8 BOM

        if (preg_match('/[\\{\\[]/', $body, $m, PREG_OFFSET_CAPTURE)) {
            $body = substr($body, (int) $m[0][1]);
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'message' => __('Gagal memuat data wilayah. Coba lagi nanti.'),
            ], 502);
        }

        return response()
            ->json($data)
            ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function provinces(): JsonResponse
    {
        return $this->fetchJson('provinces.json');
    }

    public function regencies(string $kode): JsonResponse
    {
        return $this->fetchJson('regencies/'.$kode.'.json');
    }

    public function districts(string $kode): JsonResponse
    {
        return $this->fetchJson('districts/'.$kode.'.json');
    }

    public function villages(string $kode): JsonResponse
    {
        return $this->fetchJson('villages/'.$kode.'.json');
    }
}
