<?php

namespace Tests\Unit;

use App\Support\TextChunker;
use PHPUnit\Framework\TestCase;

class TextChunkerTest extends TestCase
{
    public function test_it_splits_long_text_with_overlap(): void
    {
        $text = str_repeat('abcdefghij', 20);
        $chunks = (new TextChunker)->chunk($text, 50, 10);

        $this->assertGreaterThan(1, count($chunks));
        $this->assertTrue(strlen($chunks[0]) <= 50);
    }

    public function test_short_text_stays_one_chunk(): void
    {
        $chunks = (new TextChunker)->chunk('hello world', 800, 120);

        $this->assertSame(['hello world'], $chunks);
    }
}
