<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\WorkspaceRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Services\Usage\UsageRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        return WorkspaceResource::collection(
            $request->user()->workspaces()->orderBy('name')->get()
        );
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        $workspace = Workspace::query()->create([
            'name' => $request->string('name'),
            'slug' => Str::slug($request->string('name').'-'.Str::random(6)),
            'owner_id' => $request->user()->id,
        ]);

        $workspace->members()->attach($request->user()->id, ['role' => WorkspaceRole::Owner->value]);

        return (new WorkspaceResource($workspace->load('members')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Workspace $workspace, UsageRecorder $usage): JsonResponse
    {
        $this->authorize('view', $workspace);

        return response()->json([
            'workspace' => new WorkspaceResource($workspace),
            'usage' => [
                'tokens_this_month' => $usage->tokensThisMonth($workspace),
                'monthly_token_limit' => $workspace->monthly_token_limit,
                'remaining_tokens' => $usage->remainingTokens($workspace),
            ],
        ]);
    }
}
