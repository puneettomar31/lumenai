<?php

namespace App\Services\Usage;

use App\Models\UsageEvent;
use App\Models\User;
use App\Models\Workspace;

class UsageRecorder
{
    public function record(
        Workspace $workspace,
        string $feature,
        int $promptTokens,
        int $completionTokens,
        ?User $user = null,
        array $meta = [],
    ): UsageEvent {
        return UsageEvent::query()->create([
            'workspace_id' => $workspace->id,
            'user_id' => $user?->id,
            'feature' => $feature,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'meta' => $meta,
        ]);
    }

    public function tokensThisMonth(Workspace $workspace): int
    {
        return (int) $workspace->usageEvents()
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_tokens');
    }

    public function remainingTokens(Workspace $workspace): int
    {
        return max($workspace->monthly_token_limit - $this->tokensThisMonth($workspace), 0);
    }
}
