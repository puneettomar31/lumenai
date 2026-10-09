<?php

namespace App\Ai\Agents;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Workspace;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Tools\SimilaritySearch;
use Stringable;

class WorkspaceAssistant implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public Workspace $workspace,
        public User $user,
        public Conversation $conversation,
        public string $retrievedContext = '',
    ) {}

    public function instructions(): Stringable|string
    {
        return <<<PROMPT
You are LumenAI, a workspace assistant for a SaaS knowledge product.
Answer using the retrieved knowledge-base context when it is relevant.
If the context does not contain the answer, say so and avoid inventing facts.
Cite document titles when you use them.

Retrieved context:
{$this->retrievedContext}
PROMPT;
    }

    public function messages(): iterable
    {
        return $this->conversation->messages()
            ->orderBy('id')
            ->get()
            ->map(fn ($message) => new Message($message->role, $message->content));
    }

    public function tools(): iterable
    {
        return [
            new SimilaritySearch(using: function (string $query) {
                return $this->workspace->documents()
                    ->where('status', 'ready')
                    ->where('title', 'like', '%'.$query.'%')
                    ->limit(5)
                    ->get(['id', 'title', 'content']);
            }),
        ];
    }
}
