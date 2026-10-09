<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workspace = $this->attributes->get('workspace') ?? $this->route('workspace');

        return $this->user()?->can('ingest', $workspace) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'min:20'],
            'source' => ['nullable', 'string', 'max:40'],
        ];
    }
}
