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

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->title = $this->faker->text(30);
        $this->content = $this->faker->paragraph(10);
        $this->category_id = Category::all()->random()->id;
        $this->user = User::all()->random();
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
}
