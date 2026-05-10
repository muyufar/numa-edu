<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NominatimProxyController extends Controller
{
    private function nominatimUserAgent(): string
    {
        $name = (string) config('app.name', 'Laravel');
        $url = (string) config('app.url', 'http://localhost');

        return "{$name} ({$url})";
    }

    public function reverse(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $lat = (float) $request->query('lat');
        $lon = (float) $request->query('lon');

        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => $this->nominatimUserAgent()])
            ->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $lat,
                'lon' => $lon,
                'format' => 'jsonv2',
                'accept-language' => 'id',
            ]);

        if (! $response->successful()) {
            return response()->json(['error' => __('Layanan peta sementara tidak tersedia.')], 502);
        }

        return response()->json($response->json());
    }

    /**
     * Pencarian alamat (forward geocode), dibatasi ke Indonesia.
     *
     * @see https://operations.osmfoundation.org/policies/nominatim/
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:200'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ]);

        $q = $validated['q'];
        $limit = (int) ($validated['limit'] ?? 8);

        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => $this->nominatimUserAgent()])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $q,
                'format' => 'jsonv2',
                'limit' => $limit,
                'accept-language' => 'id',
                'countrycodes' => 'id',
            ]);

        if (! $response->successful()) {
            return response()->json(['error' => __('Layanan pencarian sementara tidak tersedia.')], 502);
        }

        $data = $response->json();

        return response()->json(is_array($data) ? $data : []);
    }
}
