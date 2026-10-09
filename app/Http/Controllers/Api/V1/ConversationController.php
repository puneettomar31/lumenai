<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Http\Requests\Api\StoreConversationRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Workspace;
use App\Services\Chat\WorkspaceChatService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        return response()->json(
            $workspace->conversations()
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate(20)
        );
    }

    public function store(StoreConversationRequest $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $conversation = $workspace->conversations()->create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
        ]);

        return response()->json($conversation, 201);
    }

    public function show(Request $request, Workspace $workspace, Conversation $conversation)
    {
        abort_unless($conversation->workspace_id === $workspace->id, 404);
        $this->authorize('view', $conversation);

        $conversation->load('messages');

        return response()->json([
            'conversation' => $conversation->only(['id', 'title', 'created_at']),
            'messages' => MessageResource::collection($conversation->messages),
        ]);
    }

    public function message(
        SendMessageRequest $request,
        Workspace $workspace,
        Conversation $conversation,
        WorkspaceChatService $chat,
    ) {
        abort_unless($conversation->workspace_id === $workspace->id, 404);

        $result = $chat->ask($workspace, $request->user(), $conversation, $request->string('content')->toString());

        return response()->json([
            'message' => new MessageResource($result['message']),
            'citations' => $result['citations'],
        ]);
    }
}
