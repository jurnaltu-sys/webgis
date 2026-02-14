<?php

namespace App\Services;

class CollaborativeFilteringService
{
    /**
     * Hitung rata-rata rating per user (abaikan missing/null)
     * @param array $ratings  Format: [ 'user1' => ['item1' => 5, 'item2' => null, ...], ... ]
     * @return array  ['user1' => 3.5, ...]
     */
    public function computeUserAverages(array $ratings): array
    {
        $averages = [];
        foreach ($ratings as $user => $items) {
            $sum = 0.0;
            $count = 0;
            foreach ($items as $val) {
                if ($val === null) continue;
                $sum += $val;
                $count++;
            }
            $averages[$user] = $count > 0 ? $sum / $count : 0.0;
        }
        return $averages;
    }

    /**
     * Normalisasi matriks dengan subtracting row mean
     * @param array $ratings
     * @param array $averages
     * @return array  same shape as $ratings, values may be negative or null
     */
    public function normalizeRatings(array $ratings, array $averages): array
    {
        $normalized = [];
        foreach ($ratings as $user => $items) {
            $normalized[$user] = [];
            $mean = $averages[$user] ?? 0.0;
            foreach ($items as $item => $val) {
                if ($val === null) {
                    $normalized[$user][$item] = null;
                } else {
                    $normalized[$user][$item] = $val - $mean;
                }
            }
        }
        return $normalized;
    }

    /**
     * Hitung centered cosine similarity antar user
     * @param array $normalizedRatings  hasil dari normalizeRatings
     * @return array matrix sim: ['u1' => ['u1' => 1.0, 'u2' => 0.5, ...], ...]
     */
    public function computeCenteredCosineSimilarity(array $normalizedRatings): array
    {
        $users = array_keys($normalizedRatings);
        $sim = [];
        foreach ($users as $u) {
            $sim[$u] = [];
            foreach ($users as $v) {
                if ($u === $v) {
                    $sim[$u][$v] = 1.0;
                    continue;
                }
                $dot = 0.0;
                $normU = 0.0;
                $normV = 0.0;
                // iterate over items present in either user; only co-rated count
                $items = array_unique(array_merge(array_keys($normalizedRatings[$u]), array_keys($normalizedRatings[$v])));
                $hasCoRated = false;
                foreach ($items as $item) {
                    $a = $normalizedRatings[$u][$item] ?? null;
                    $b = $normalizedRatings[$v][$item] ?? null;
                    if ($a === null || $b === null) continue;
                    $hasCoRated = true;
                    $dot += $a * $b;
                    $normU += $a * $a;
                    $normV += $b * $b;
                }
                if (!$hasCoRated || $normU == 0.0 || $normV == 0.0) {
                    $sim[$u][$v] = 0.0;
                } else {
                    $sim[$u][$v] = $dot / (sqrt($normU) * sqrt($normV));
                }
            }
        }
        return $sim;
    }

    /**
     * Untuk setiap user, kembalikan list tetangga terurut berdasarkan similarity
     * @param array $simMatrix
     * @param int $k
     * @param bool $onlyPositive  hanya ambil neighbor dengan sim>0 jika true
     * @return array  ['u1' => [['user'=>'u2','sim'=>0.9], ...], ...]
     */
    public function getTopNeighbors(array $simMatrix, int $k = 1, bool $onlyPositive = true): array
    {
        $result = [];
        foreach ($simMatrix as $u => $row) {
            $neighbors = [];
            foreach ($row as $v => $score) {
                if ($u === $v) continue;
                if ($onlyPositive && $score <= 0.0) continue;
                $neighbors[] = ['user' => $v, 'sim' => $score];
            }
            usort($neighbors, function ($a, $b) {
                if ($a['sim'] == $b['sim']) return 0;
                return ($a['sim'] > $b['sim']) ? -1 : 1;
            });
            $result[$u] = array_slice($neighbors, 0, $k);
        }
        return $result;
    }
}
