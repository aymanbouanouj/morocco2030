<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_boots_with_an_empty_database(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
