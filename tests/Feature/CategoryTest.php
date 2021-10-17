<?php

namespace Tests\Feature;

use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_anyone_can_see_categories()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
    }
}
