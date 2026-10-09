<?php

namespace App\Mcp\Tools;

use App\Models\Workspace;
use App\Services\Rag\VectorRetriever;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Search the authenticated workspace knowledge base with RAG embeddings.')]
#[IsReadOnly]
class SearchKnowledgeBaseTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'workspace_id' => $schema->integer()->description('Workspace ID')->required(),
            'query' => $schema->string()->description('Natural language search query')->required(),
        ];
    }

    public function handle(Request $request, VectorRetriever $retriever): Response
    {
        $validated = $request->validate([
            'workspace_id' => ['required', 'integer', 'exists:workspaces,id'],
            'query' => ['required', 'string', 'min:2'],
        ]);

        $workspace = Workspace::query()->findOrFail($validated['workspace_id']);
        $user = $request->user();

        if (! $user || ! $user->can('view', $workspace)) {
            return Response::error('You do not have access to this workspace.');
        }

        $hits = $retriever->search($workspace, $validated['query']);

        return Response::structured([
            'query' => $validated['query'],
            'hits' => $hits,
        ]);
    }
}
