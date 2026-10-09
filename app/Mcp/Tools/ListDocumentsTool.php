<?php

namespace App\Mcp\Tools;

use App\Models\Workspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List ingested knowledge documents in a workspace.')]
#[IsReadOnly]
class ListDocumentsTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'workspace_id' => $schema->integer()->description('Workspace ID')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'workspace_id' => ['required', 'integer', 'exists:workspaces,id'],
        ]);

        $workspace = Workspace::query()->findOrFail($validated['workspace_id']);

        if (! $request->user()?->can('view', $workspace)) {
            return Response::error('You do not have access to this workspace.');
        }

        return Response::structured([
            'documents' => $workspace->documents()
                ->latest()
                ->get(['id', 'title', 'status', 'chunk_count', 'created_at'])
                ->all(),
        ]);
    }
}
