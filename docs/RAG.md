# How RAG is built and how to run it

RAG here means: **retrieve workspace document chunks, then generate an answer that cites them**.

## Pipeline

```text
Document text
    → TextChunker (size 800, overlap 120)
    → Laravel AI SDK Embeddings::for($chunks)->generate()
    → document_chunks.embedding (JSON float array)
    → question embedding
    → cosine similarity (top_k, min_similarity)
    → WorkspaceAssistant prompt with retrieved context
```

### Code map

| Step | File |
| --- | --- |
| Chunk | `app/Support/TextChunker.php` |
| Embed + save | `app/Services/Rag/DocumentIngestor.php` |
| Search | `app/Services/Rag/VectorRetriever.php` |
| Score | `app/Support/CosineSimilarity.php` |
| Answer | `app/Ai/Agents/WorkspaceAssistant.php` |
| HTTP | `DocumentController`, `ConversationController` |

## Run it locally

1. Create MySQL database `lumenai`.
2. Copy `.env.example` to `.env`, set `DB_*` and `OPENAI_API_KEY`.
3. Install and migrate:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

4. Register and keep the token + workspace id:

```bash
curl -X POST http://localhost:8000/api/v1/auth/register ^
  -H "Content-Type: application/json" ^
  -d "{\"name\":\"Ada\",\"email\":\"ada@lumenai.test\",\"password\":\"Password123!\",\"password_confirmation\":\"Password123!\",\"workspace_name\":\"Demo\"}"
```

5. Ingest knowledge (this calls embeddings):

```bash
curl -X POST http://localhost:8000/api/v1/workspaces/WORKSPACE_ID/documents ^
  -H "Authorization: Bearer TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"title\":\"Refund policy\",\"content\":\"Customers can request a refund within 14 days of purchase if usage is under 10 percent of the monthly quota.\"}"
```

Status becomes `ready` when chunks exist.

6. Create a conversation and ask:

```bash
curl -X POST http://localhost:8000/api/v1/workspaces/WORKSPACE_ID/conversations ^
  -H "Authorization: Bearer TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"title\":\"Policy questions\"}"

curl -X POST http://localhost:8000/api/v1/workspaces/WORKSPACE_ID/conversations/CONVERSATION_ID/messages ^
  -H "Authorization: Bearer TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"content\":\"What is the refund window?\"}"
```

The JSON `citations` array is the retrieved chunks (title + score). That is how you verify RAG, not just the model’s prose.

## Tests without calling OpenAI

Unit tests cover chunking and cosine math:

```bash
php artisan test --testsuite=Unit
```

Feature ingest/chat tests should call `Laravel\Ai\Embeddings::fake()` after Composer has installed `laravel/ai`.

## Tuning

Set in `.env`:

- `RAG_CHUNK_SIZE` / `RAG_CHUNK_OVERLAP`
- `RAG_TOP_K` / `RAG_MIN_SIMILARITY`
- `AI_EMBEDDING_MODEL` / `AI_EMBEDDING_DIMENSIONS`
