<?php

namespace Tests\Feature;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_list_workspaces(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $workspace->members()->attach($user->id, ['role' => WorkspaceRole::Owner->value]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/workspaces')
            ->assertOk()
            ->assertJsonFragment(['name' => $workspace->name]);
    }

    public function test_stranger_cannot_view_workspace(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->members()->attach($owner->id, ['role' => WorkspaceRole::Owner->value]);

        Sanctum::actingAs($stranger);

        $this->getJson('/api/v1/workspaces/'.$workspace->id)
            ->assertForbidden();
    }
}
