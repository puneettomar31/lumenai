<?php

namespace App\Mcp\Resources;

use App\Models\Workspace;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;

#[Description('Overview of workspaces the authenticated user can access.')]
class WorkspaceOverviewResource extends Resource
{
    public function handle(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return Response::error('Authentication required.');
        }

        $workspaces = Workspace::query()
            ->whereHas('members', fn ($query) => $query->where('user_id', $user->id))
            ->get(['id', 'name', 'slug']);

        return Response::text($workspaces->toJson(JSON_PRETTY_PRINT));
    }
}
