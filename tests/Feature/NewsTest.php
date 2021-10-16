<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $title;
    private $content;
    private $category_id;
    private $news;
    private $user;
    private $notOwnerUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = User::all()->random();
        $this->notOwnerUser = User::where('id', '<>', $this->user->id)->inRandomOrder()->first();

        $this->title = $this->faker->text(30);
        $this->content = $this->faker->paragraph(10);
        $this->category_id = Category::all()->random()->id;
        $this->news = News::factory(['user_id' => $this->user->id])->create();
    }

    public function test_anyone_can_see_all_the_news()
    {
        $response = $this->getJson('/api/news');

        $response->assertStatus(200);
    }

    public function test_anyone_can_get_the_news_by_id()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/news/' . $this->news->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'author' => $this->user->name,
                    'title' => $this->news->title,
                    'content' => $this->news->content,
                    'category_id' => $this->news->category_id,
                ]
            ]);
    }

    public function test_user_can_create_the_news()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/news', [
            'title' => $this->title,
            'content' => $this->content,
            'category_id' => $this->category_id,
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'author' => $this->user->name,
                    'title' => $this->title,
                    'content' => $this->content,
                    'category_id' => $this->category_id,

                ]
            ]);
    }

    public function test_user_can_update_the_news()
    {
        Sanctum::actingAs($this->user);

        $newCategoryId = Category::all()->random()->id;

        $response = $this->putJson('/api/news/' . $this->news->id, [
            'title' => 'Some another title',
            'content' => 'Some another content',
            'category_id' => $newCategoryId,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'author' => $this->user->name,
                    'title' => 'Some another title',
                    'content' => 'Some another content',
                    'category_id' => $newCategoryId,

                ]
            ]);
    }

    public function test_user_can_delete_the_news()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/news/' . $this->news->id);

        $response->assertStatus(204);
    }

    public function test_not_authenticated_user_cannot_create_the_news()
    {
        $response = $this->postJson('/api/news', [
            'title' => $this->title,
            'content' => $this->content,
            'category_id' => $this->category_id,
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_not_authenticated_user_cannot_update_the_news()
    {
        $response = $this->putJson('/api/news/' . $this->news->id, [
            'title' => 'Some title',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_not_authenticated_user_cannot_delete_the_news()
    {
        $response = $this->deleteJson('/api/news/' . $this->news->id);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_not_owner_cannot_update_the_news()
    {
        Sanctum::actingAs($this->notOwnerUser);

        $response = $this->putJson('/api/news/' . $this->news->id, [
            'content' => 'Some content',
        ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
    }

    public function test_not_owner_cannot_delete_the_news()
    {
        Sanctum::actingAs($this->notOwnerUser);

        $response = $this->deleteJson('/api/news/' . $this->news->id);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
    }
}
