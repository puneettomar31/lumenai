<?php

namespace App\Services\Rag;

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Support\TextChunker;
use Laravel\Ai\Embeddings;

class DocumentIngestor
{
    public function __construct(private TextChunker $chunker) {}

    public function ingest(Document $document): Document
    {
        $document->update(['status' => 'processing']);
        $document->chunks()->delete();

        $size = (int) config('lumenai.rag.chunk_size');
        $overlap = (int) config('lumenai.rag.chunk_overlap');
        $chunks = $this->chunker->chunk($document->content, $size, $overlap);

        if ($chunks === []) {
            $document->update(['status' => 'failed', 'chunk_count' => 0]);

            return $document->refresh();
        }

        $response = Embeddings::for($chunks)
            ->dimensions((int) config('lumenai.embedding_dimensions'))
            ->generate();

        foreach ($chunks as $index => $content) {
            DocumentChunk::query()->create([
                'workspace_id' => $document->workspace_id,
                'document_id' => $document->id,
                'chunk_index' => $index,
                'content' => $content,
                'embedding' => $response->embeddings[$index] ?? [],
                'token_count' => str_word_count($content),
            ]);
        }

        $document->update([
            'status' => 'ready',
            'chunk_count' => count($chunks),
        ]);

        return $document->refresh();
    }
}
