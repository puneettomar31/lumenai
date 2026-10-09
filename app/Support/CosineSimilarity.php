<?php

namespace App\Support;

class CosineSimilarity
{
    /**
     * @param  list<float|int>  $a
     * @param  list<float|int>  $b
     */
    public function score(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $count = min(count($a), count($b));

        for ($i = 0; $i < $count; $i++) {
            $left = (float) $a[$i];
            $right = (float) $b[$i];
            $dot += $left * $right;
            $normA += $left * $left;
            $normB += $right * $right;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        if ($denominator <= 0.0) {
            return 0.0;
        }

        return $dot / $denominator;
    }
}
