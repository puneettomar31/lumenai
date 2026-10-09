# LumenAI

Laravel 13 AI SaaS workspace: multi-tenant workspaces, Sanctum REST APIs, Form Request validation, policies, **Laravel AI SDK** agents + embeddings, **RAG**, and **Laravel MCP**.

## What you get

- Auth + authorization (Sanctum tokens, workspace roles)
- MySQL schema for users, workspaces, documents, chunks, chats, usage
- RAG ingest/search documented in [docs/RAG.md](docs/RAG.md)
- MCP server documented in [docs/MCP.md](docs/MCP.md)
- System design in [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)
- Deploy from GitHub `main` in [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)

## Requirements

- PHP 8.3+
- Composer
- MySQL 8
- OpenAI key (or another provider configured in Laravel AI SDK)

## Quick start

```bash
cd lumenai
copy .env.example .env
composer install
php artisan key:generate
```

Create database `lumenai`, then:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

Demo user from seeder: `owner@lumenai.test` / `password`.

If Composer cannot resolve `laravel/ai` or `laravel/mcp` versions, install the current tags explicitly:

```bash
composer require laravel/ai laravel/mcp laravel/sanctum
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
```

## API sketch

| Method | Path | Auth |
| --- | --- | --- |
| POST | `/api/v1/auth/register` | no |
| POST | `/api/v1/auth/login` | no |
| GET | `/api/v1/me` | Sanctum |
| GET/POST | `/api/v1/workspaces` | Sanctum |
| POST | `/api/v1/workspaces/{id}/documents` | Sanctum, admin/owner |
| POST | `/api/v1/workspaces/{id}/conversations/{id}/messages` | Sanctum |
| MCP | `/mcp` | Sanctum |

Validation lives in `app/Http/Requests/Api`. Authorization lives in `app/Policies`.

## Project layout

```text
app/Ai/Agents          Laravel AI SDK agent
app/Http/Requests/Api  Separate validation
app/Http/Resources     API shape
app/Mcp                MCP server, tools, prompts
app/Services/Rag       Ingest + retrieve
docs/                  Architecture, RAG, MCP, deploy
```

## Tests

```bash
php artisan test
```
