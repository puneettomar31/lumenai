<?php

return [
    'embedding_model' => env('AI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    'embedding_dimensions' => (int) env('AI_EMBEDDING_DIMENSIONS', 1536),
    'chat_model' => env('AI_CHAT_MODEL', 'gpt-4.1-mini'),
    'rag' => [
        'chunk_size' => (int) env('RAG_CHUNK_SIZE', 800),
        'chunk_overlap' => (int) env('RAG_CHUNK_OVERLAP', 120),
        'top_k' => (int) env('RAG_TOP_K', 5),
        'min_similarity' => (float) env('RAG_MIN_SIMILARITY', 0.35),
    ],
];
