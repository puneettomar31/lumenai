<?php

namespace Tests\Unit;

use App\Support\CosineSimilarity;
use PHPUnit\Framework\TestCase;

class CosineSimilarityTest extends TestCase
{
    public function test_identical_vectors_score_one(): void
    {
        $score = (new CosineSimilarity)->score([1.0, 0.0, 0.0], [1.0, 0.0, 0.0]);

        $this->assertEqualsWithDelta(1.0, $score, 0.0001);
    }

    public function test_orthogonal_vectors_score_zero(): void
    {
        $score = (new CosineSimilarity)->score([1.0, 0.0], [0.0, 1.0]);

        $this->assertEqualsWithDelta(0.0, $score, 0.0001);
    }
}
