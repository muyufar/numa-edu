<?php

namespace App\Support;

class FaceDescriptorMatcher
{
    public const DEFAULT_THRESHOLD = 0.55;

    /**
     * @param  list<float>  $probe
     * @param  list<float>  $reference
     */
    public static function distance(array $probe, array $reference): float
    {
        $sum = 0.0;
        $count = min(count($probe), count($reference));

        for ($i = 0; $i < $count; $i++) {
            $diff = (float) $probe[$i] - (float) $reference[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    /**
     * @param  list<float>  $probe
     * @param  iterable<int, array{id: int, nama: string, descriptor: list<float>}>  $candidates
     * @return array{id: int, nama: string, distance: float}|null
     */
    public static function bestMatch(array $probe, iterable $candidates, float $threshold = self::DEFAULT_THRESHOLD): ?array
    {
        $best = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach ($candidates as $candidate) {
            $distance = self::distance($probe, $candidate['descriptor']);

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = [
                    'id' => $candidate['id'],
                    'nama' => $candidate['nama'],
                    'distance' => $distance,
                ];
            }
        }

        if ($best === null || $best['distance'] > $threshold) {
            return null;
        }

        return $best;
    }
}
