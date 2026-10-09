# LumenAI system design

LumenAI is a multi-tenant AI SaaS workspace: teams ingest documents, retrieve them with RAG, chat through the Laravel AI SDK, and expose the same capabilities to external agents through Laravel MCP.

## Goals

- Laravel 13, PHP 8.3, MySQL, Sanctum token APIs
- Clean HTTP layer: Form Requests, API Resources, Policies
- RAG as an explicit pipeline (chunk → embed → store → retrieve → generate)
- MCP server for Cursor / Claude / ChatGPT-style clients
- Deployable from `main` with Docker + GitHub Actions

## Context diagram

```text
                    ┌──────────────┐
   Browser / SPA ──►│ REST /api/v1 │
                    └──────┬───────┘
Cursor / Claude ──► MCP /mcp┤
                    ┌──────▼───────┐
                    │  Domain      │
                    │  Workspaces  │
                    │  AuthZ       │
                    └──┬───────┬───┘
           ┌───────────▼──┐ ┌──▼────────────┐
           │ RAG pipeline │ │ Laravel AI    │
           │ chunk/embed  │ │ Agent + tools │
           └──────┬───────┘ └───────┬───────┘
                  │                 │
           MySQL documents    OpenAI (or other lab)
           JSON embeddings
```

## Bounded contexts

| Context | Code | Responsibility |
| --- | --- | --- |
| Identity | `AuthController`, `User` | Register, login, Sanctum tokens |
| Tenancy | `Workspace`, policies, `workspace_user.role` | Owner / admin / member |
| Knowledge | `Document`, `DocumentChunk`, `DocumentIngestor` | Ingest and index |
| Retrieval | `VectorRetriever`, `CosineSimilarity` | Top-k similar chunks |
| Conversation | `WorkspaceAssistant`, `WorkspaceChatService` | RAG-grounded answers + usage |
| MCP | `app/Mcp` | External agent tools/resources/prompts |

## Request flow (chat)

1. Client sends `POST /api/v1/workspaces/{id}/conversations/{id}/messages` with a Sanctum token.
2. `SendMessageRequest` validates input; `ConversationPolicy` authorizes.
3. `VectorRetriever` embeds the question (`Laravel\Ai\Embeddings`) and scores MySQL JSON vectors with cosine similarity.
4. `WorkspaceAssistant` (Laravel AI SDK agent) answers using retrieved context and optional tools.
5. Tokens are written to `usage_events` for SaaS limits.

## Authorization model

- Authentication: Laravel Sanctum personal access tokens
- Authorization: policies on workspace membership
- Roles: `owner`, `admin`, `member` (`App\Enums\WorkspaceRole`)
- Ingest (write knowledge): owner and admin only
- Chat / read: any member

## Why MySQL JSON embeddings

Laravel's native `vector` column + `whereVectorSimilarTo` targets PostgreSQL (`pgvector`) and MariaDB. This product uses **MySQL** as requested, so embeddings are stored as JSON and ranked in `VectorRetriever`. That keeps local/dev simple. For large production indexes, switch the retriever to pgvector/MariaDB vector without changing the HTTP API.

## Scaling notes

- Move ingest to a queued job (`ShouldQueue`) once files are large
- Add Redis for cache/queues in production
- Replace in-process cosine scan with an HNSW vector index when chunk volume grows
- Keep REST and MCP on the same domain services so behavior stays identical
