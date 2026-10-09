<?php

namespace App\Services\Rag;

use App\Models\DocumentChunk;
use App\Models\Workspace;
use App\Support\CosineSimilarity;
use Laravel\Ai\Embeddings;

class VectorRetriever
{
    public function __construct(private CosineSimilarity $similarity) {}

    /**
     * Retrieve the most similar chunks for a workspace query (MySQL JSON embeddings).
     *
     * @return list<array{content: string, score: float, document_id: int, title: string}>
     */
    public function search(Workspace $workspace, string $query, ?int $limit = null): array
    {
        $limit ??= (int) config('lumenai.rag.top_k');
        $minSimilarity = (float) config('lumenai.rag.min_similarity');

        $queryEmbedding = Embeddings::for([$query])
            ->dimensions((int) config('lumenai.embedding_dimensions'))
            ->generate()
            ->embeddings[0] ?? [];

        $ranked = [];

        DocumentChunk::query()
            ->with('document:id,title')
            ->where('workspace_id', $workspace->id)
            ->orderBy('id')
            ->chunkById(200, function ($chunks) use ($queryEmbedding, $minSimilarity, &$ranked) {
                foreach ($chunks as $chunk) {
                    $score = $this->similarity->score($queryEmbedding, $chunk->embedding ?? []);

                    if ($score >= $minSimilarity) {
                        $ranked[] = [
                            'content' => $chunk->content,
                            'score' => round($score, 4),
                            'document_id' => $chunk->document_id,
                            'title' => $chunk->document?->title ?? 'Untitled',
                        ];
                    }
                }
            });

        usort($ranked, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($ranked, 0, $limit);
    }

    public function contextBlock(array $hits): string
    {
        if ($hits === []) {
            return 'No knowledge-base context was found.';
        }

        return collect($hits)
            ->map(fn (array $hit, int $i) => sprintf(
                "[%d] (%s, score %s)\n%s",
                $i + 1,
                $hit['title'],
                $hit['score'],
                $hit['content'],
            ))
            ->implode("\n\n");
    }
}
