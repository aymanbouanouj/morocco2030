<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationSecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_compatibility_endpoint_does_not_persist_subscriber_data(): void
    {
        $path = storage_path('app/newsletter_subscribers.json');

        $this->assertFileDoesNotExist($path);

        $this->from(route('home'))
            ->post(route('newsletter.subscribe'), ['email' => 'reader@example.test'])
            ->assertRedirect(route('home'))
            ->assertSessionHas('newsletter_status');

        $this->assertFileDoesNotExist($path);
    }

    public function test_page_header_escapes_plain_action_markup(): void
    {
        $html = view('public.partials.page-header', [
            'title' => 'Publication safety',
            'actions' => '<script>alert("unsafe")</script>',
        ])->render();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
