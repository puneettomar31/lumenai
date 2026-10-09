<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $document->workspace->hasMember($user);
    }

    public function delete(User $user, Document $document): bool
    {
        return $document->workspace->roleFor($user)?->canIngest() ?? false;
    }
}
