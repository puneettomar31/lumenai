<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\AskKnowledgeBasePrompt;
use App\Mcp\Resources\WorkspaceOverviewResource;
use App\Mcp\Tools\ListDocumentsTool;
use App\Mcp\Tools\SearchKnowledgeBaseTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('LumenAI')]
#[Version('1.0.0')]
#[Instructions('LumenAI MCP server: search workspace knowledge, list documents, and ask RAG-backed questions.')]
class LumenAiServer extends Server
{
    /**
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        SearchKnowledgeBaseTool::class,
        ListDocumentsTool::class,
    ];

    /**
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        WorkspaceOverviewResource::class,
    ];

    /**
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        AskKnowledgeBasePrompt::class,
    ];
}
