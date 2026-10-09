<?php

namespace Database\Seeders;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Demo Owner',
            'email' => 'owner@lumenai.test',
            'password' => 'password',
        ]);

        $workspace = Workspace::query()->create([
            'name' => 'Demo Workspace',
            'slug' => 'demo-'.Str::lower(Str::random(4)),
            'owner_id' => $user->id,
        ]);

        $workspace->members()->attach($user->id, ['role' => WorkspaceRole::Owner->value]);
    }
}
