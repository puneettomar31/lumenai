<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class AskKnowledgeBasePrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'question',
                description: 'The question to answer from the workspace knowledge base.',
                required: true,
            ),
        ];
    }

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        return Response::text(
            "Use the search-knowledge-base tool first, then answer this question using only retrieved context:\n\n{$validated['question']}"
        );
    }
}
