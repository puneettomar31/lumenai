<?php

namespace App\Services\Chat;

use App\Ai\Agents\WorkspaceAssistant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Rag\VectorRetriever;
use App\Services\Usage\UsageRecorder;

class WorkspaceChatService
{
    public function __construct(
        private VectorRetriever $retriever,
        private UsageRecorder $usage,
    ) {}

    /**
     * @return array{message: Message, citations: array<int, mixed>}
     */
    public function ask(Workspace $workspace, User $user, Conversation $conversation, string $prompt): array
    {
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $prompt,
        ]);

        $hits = $this->retriever->search($workspace, $prompt);
        $context = $this->retriever->contextBlock($hits);

        $agent = new WorkspaceAssistant($workspace, $user, $conversation, $context);
        $response = $agent->prompt($prompt);
        $text = is_string($response->text ?? null) ? $response->text : (string) $response;

        $promptTokens = (int) ($response->usage->promptTokens ?? $response->usage['prompt_tokens'] ?? str_word_count($prompt));
        $completionTokens = (int) ($response->usage->completionTokens ?? $response->usage['completion_tokens'] ?? str_word_count($text));

        $message = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $text,
            'citations' => $hits,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
        ]);

        if (! $conversation->title) {
            $conversation->update(['title' => mb_substr($prompt, 0, 80)]);
        }

        $this->usage->record(
            $workspace,
            'chat',
            $promptTokens,
            $completionTokens,
            $user,
            ['conversation_id' => $conversation->id],
        );

        return [
            'message' => $message,
            'citations' => $hits,
        ];
    }
}
