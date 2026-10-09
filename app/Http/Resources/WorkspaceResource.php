<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'monthly_token_limit' => $this->monthly_token_limit,
            'role' => $this->whenPivotLoaded('workspace_user', fn () => $this->pivot->role),
            'created_at' => $this->created_at,
        ];
    }
}
