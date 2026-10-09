<?php

namespace App\Support;

class TextChunker
{
    /**
     * Split text into overlapping windows so RAG retrieval keeps local context.
     *
     * @return list<string>
     */
    public function chunk(string $text, int $size, int $overlap): array
    {
        $normalized = trim(preg_replace("/\r\n|\r/", "\n", $text) ?? $text);

        if ($normalized === '') {
            return [];
        }

        if (mb_strlen($normalized) <= $size) {
            return [$normalized];
        }

        $chunks = [];
        $start = 0;
        $length = mb_strlen($normalized);

        while ($start < $length) {
            $chunk = mb_substr($normalized, $start, $size);
            $chunks[] = trim($chunk);
            $start += max($size - $overlap, 1);
        }

        return array_values(array_filter($chunks));
    }
}
