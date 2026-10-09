# MCP server

LumenAI uses official **Laravel MCP** (`laravel/mcp`) so Cursor, Claude Desktop, or any MCP client can call the same knowledge tools as the REST API.

## What is exposed

| Kind | Class | Purpose |
| --- | --- | --- |
| Server | `App\Mcp\Servers\LumenAiServer` | Registry |
| Tool | `SearchKnowledgeBaseTool` | RAG search |
| Tool | `ListDocumentsTool` | List ingested docs |
| Resource | `WorkspaceOverviewResource` | Workspaces for the user |
| Prompt | `AskKnowledgeBasePrompt` | Suggested RAG question flow |

Routes: `routes/ai.php`

- HTTP: `POST /mcp` (Sanctum)
- Local STDIO: `php artisan mcp:start lumenai`

## Run over HTTP

1. Create a Sanctum token (login/register API).
2. Point the MCP client at `http://localhost:8000/mcp`.
3. Send `Authorization: Bearer TOKEN`.

Example Cursor MCP config (`mcp.json`):

```json
{
  "mcpServers": {
    "lumenai": {
      "url": "http://localhost:8000/mcp",
      "headers": {
        "Authorization": "Bearer YOUR_TOKEN"
      }
    }
  }
}
```

## Run locally (STDIO)

```bash
php artisan mcp:start lumenai
php artisan mcp:inspector
```

The inspector is the standard way to see tool schemas and sample calls.

## Agents inside the app

The Laravel AI SDK can also *consume* MCP tools:

```php
use Laravel\Mcp\Client;

Client::web(url('/mcp'))
    ->withToken($token)
    ->tools();
```

That is documented in [Laravel AI SDK — MCP tools](https://laravel.com/docs/ai-sdk).
