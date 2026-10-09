<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $workspaceId = $request->header('X-Workspace-Id') ?? $request->route('workspace');

        abort_unless($workspaceId, 400, 'Workspace is required.');

        $workspace = Workspace::query()->findOrFail($workspaceId);
        abort_unless($request->user()?->can('view', $workspace), 403);

        $request->attributes->set('workspace', $workspace);

        return $next($request);
    }
}
