<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\Document;
use App\Models\Workspace;
use App\Policies\ConversationPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\WorkspacePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Workspace::class, WorkspacePolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
    }
}
