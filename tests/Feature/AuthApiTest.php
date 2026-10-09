<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@lumenai.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'workspace_name' => 'Analytical Engine',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'email'], 'workspace' => ['id', 'slug']]);

        $this->assertDatabaseHas('users', ['email' => 'ada@lumenai.test']);
        $this->assertDatabaseHas('workspaces', ['name' => 'Analytical Engine']);
    }

    public function test_login_rejects_bad_credentials(): void
    {
        User::factory()->create([
            'email' => 'ada@lumenai.test',
            'password' => 'Password123!',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'ada@lumenai.test',
            'password' => 'wrong',
        ])->assertUnprocessable();
    }

    public function test_register_uses_form_request_validation(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ada',
            'email' => 'not-an-email',
            'password' => 'x',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
