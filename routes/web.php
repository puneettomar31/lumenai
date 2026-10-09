<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'docs' => [
            'architecture' => '/docs/ARCHITECTURE.md (in repo)',
            'rag' => url('/up').' healthcheck; see docs/RAG.md',
            'mcp' => url('/mcp'),
            'api' => url('/api/v1'),
        ],
    ]);
});
