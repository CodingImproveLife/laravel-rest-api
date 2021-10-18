<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user_name;
    private $user_email;

    public function setUp(): void
    {
        parent::setUp();

        $this->user_name = $this->faker->name();
        $this->user_email = $this->faker->safeEmail();
    }

    public function test_user_can_sign_up()
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->user_name,
            'email' => $this->user_email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'user' => [
                    'name' => $this->user_name,
                    'email' => $this->user_email,
                ]
            ])
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'email',
                    'name',
                ],
                'access_token'
            ]);
    }

    public function test_user_can_sign_in()
    {
        User::factory(['email' => $this->user_email])->create();
        $response = $this->postJson('/api/login', [
            'email' => $this->user_email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'user' => [
                    'email' => $this->user_email,
                ]
            ])
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'email',
                    'name',
                ],
                'access_token'
            ]);
    }
}
