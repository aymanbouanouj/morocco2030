<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminNewsIndexUiTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_news_index_renders_for_super_admin(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk();
    }

    public function test_news_index_shows_page_title(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('News', false);
    }

    public function test_news_index_shows_workflow_labels(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        foreach (['Drafts', 'In Review', 'Published', 'Scheduled', 'Archived'] as $label) {
            $this->assertStringContainsString($label, $module);
        }
    }

    public function test_news_index_shows_filter_controls(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('Search title or slug', false)
            ->assertSee('All categories', false)
            ->assertSee('All statuses', false)
            ->assertSee('Filter', false);
    }

    public function test_create_news_visible_for_journalist(): void
    {
        $html = $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('admin-news-create', $html);
        $this->assertStringContainsString('Create News', $html);
    }

    public function test_create_news_visible_for_super_admin(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('admin-news-create', false)
            ->assertSee('Create News', false);
    }

    public function test_create_news_hidden_for_unauthorized_staff(): void
    {
        $html = $this->actingAs($this->demoUser('match.manager@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertForbidden()
            ->getContent();

        $this->assertStringNotContainsString('admin-news-create', $html);
        $this->assertStringNotContainsString('Create News', $html);
    }

    public function test_news_table_headers_render(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        foreach (['Title', 'Category', 'Status', 'Author', 'Published / Scheduled', 'Actions'] as $header) {
            $this->assertStringContainsString($header, $module);
        }
    }

    public function test_empty_state_renders_when_no_news(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('No news created yet.', false)
            ->assertSee('Create your first draft and submit it for review.', false)
            ->assertSee('admin-news-empty', false);
    }

    public function test_news_index_compact_toolbar_markup_present(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('admin-news-toolbar', false)
            ->assertSee('admin-news-filters', false);
    }

    public function test_news_actions_do_not_use_hash_links_when_rows_exist(): void
    {
        $category = $this->makeNewsCategory('Press');
        $author = $this->demoUser('journalist@morocco2030.test');
        $this->makeNews($author, $category, ['status' => 'draft']);

        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        $this->assertStringNotContainsString('href="#"', $module);
    }

    private function extractNewsModule(string $html): string
    {
        $start = strpos($html, 'admin-news-page');
        $this->assertNotFalse($start, 'Expected admin news module markup.');

        $end = strpos($html, '</section>', $start);
        $this->assertNotFalse($end, 'Expected admin news module closing tag.');

        return substr($html, $start, $end - $start + strlen('</section>'));
    }
}
