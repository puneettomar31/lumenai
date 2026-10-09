<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('conversation')) ?? false;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:2', 'max:8000'],
        ];
    }
}
