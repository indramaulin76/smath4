<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Filament\Resources\ArticleResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_an_article()
    {
        // Create and authenticate a user (assuming any authenticated user is an admin for this test)
        $user = User::factory()->create();
        $this->actingAs($user);

        // The data for the new article
        $newData = [
            'title' => 'My First Test Article',
            'content' => 'This is the content of the test article.',
            'excerpt' => 'A short summary of the article.',
            'is_published' => true,
            'published_at' => now()->toDateTimeString(),
        ];

        // Test the Livewire component
        Livewire::test(ArticleResource\Pages\CreateArticle::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        // Assert the article was created in the database
        $this->assertDatabaseHas('articles', [
            'title' => 'My First Test Article',
            'content' => 'This is the content of the test article.',
            'is_published' => true,
        ]);
    }
}
