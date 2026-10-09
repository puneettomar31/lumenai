<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Workspace;
use App\Services\Rag\DocumentIngestor;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        return DocumentResource::collection(
            $workspace->documents()->latest()->paginate(20)
        );
    }

    public function store(StoreDocumentRequest $request, Workspace $workspace, DocumentIngestor $ingestor)
    {
        $document = $workspace->documents()->create([
            'user_id' => $request->user()->id,
            'title' => $request->string('title'),
            'source' => $request->input('source', 'paste'),
            'status' => 'pending',
            'content' => $request->string('content'),
        ]);

        $ingestor->ingest($document);

        return (new DocumentResource($document->refresh()))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request, Workspace $workspace, Document $document)
    {
        abort_unless($document->workspace_id === $workspace->id, 404);
        $this->authorize('delete', $document);

        $document->delete();

        return response()->json(['message' => 'Document deleted.']);
    }
}
