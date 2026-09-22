<?php

namespace Tests\Feature\Admin;

use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class NewsCategoryReadinessTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_tournament_updates_category_is_created_or_reused_as_active(): void
    {
        $created = NewsCategory::ensureTournamentUpdatesCategory();
        $reused = NewsCategory::ensureTournamentUpdatesCategory();

        $this->assertTrue($created->is($reused));
        $this->assertSame('Tournament Updates', $reused->name);
        $this->assertSame('tournament-updates', $reused->slug);
        $this->assertSame('active', $reused->status);
        $this->assertSame(1, NewsCategory::query()->where('slug', 'tournament-updates')->count());
    }

    public function test_admin_news_create_form_shows_active_category_option(): void
    {
        $category = NewsCategory::ensureTournamentUpdatesCategory();

        $html = $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.news.create'))
            ->assertOk()
            ->assertSee('Save Draft', false)
            ->assertDontSee('Create at least one news category before publishing news content.', false)
            ->getContent();

        $this->assertStringContainsString('name="category_id"', $html);
        $this->assertStringContainsString('value="'.$category->id.'"', $html);
        $this->assertStringContainsString('Tournament Updates', $html);
        $this->assertStringNotContainsString('href="#"', $html);
    }
}
