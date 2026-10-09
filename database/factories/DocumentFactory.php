<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'source' => 'paste',
            'status' => 'pending',
            'content' => fake()->paragraphs(4, true),
            'chunk_count' => 0,
        ];
    }
}
