<?php

namespace Tests\Feature\Admin;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminNewsModuleHardeningTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    private string $demoPassword = 'Morocco2030-Local-Demo-Only';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_public_user_cannot_access_admin_news_index(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_access_news_index(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk();
    }

    public function test_chief_editor_can_access_news_index(): void
    {
        $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk();
    }

    public function test_journalist_can_access_news_index(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk();
    }

    public function test_unauthorized_staff_cannot_access_news_index(): void
    {
        $this->actingAs($this->demoUser('match.manager@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertForbidden();
    }

    public function test_news_index_renders_title_subtitle_and_filters(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('News', false)
            ->assertSee('Create drafts, attach media, and submit stories for editorial review.', false)
            ->assertSee('Search title or slug', false)
            ->assertSee('All categories', false)
            ->assertSee('All statuses', false);
    }

    public function test_create_news_button_visible_for_journalist(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertSee('Create News', false);
    }

    public function test_create_news_button_hidden_for_unauthorized_staff(): void
    {
        $html = $this->actingAs($this->demoUser('match.manager@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertForbidden()
            ->getContent();

        $this->assertStringNotContainsString('Create News', $html);
    }

    public function test_news_table_columns_and_scoped_wrapper_render(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        foreach (['Title', 'Category', 'Status', 'Author', 'Published / Scheduled', 'Actions'] as $column) {
            $this->assertStringContainsString($column, $module);
        }

        $this->assertStringContainsString('admin-news-table-wrap--fit', $module);
        $this->assertStringContainsString('admin-news-col-actions', $module);
        $this->assertStringContainsString('admin-news-col-actions-head', $module);
    }

    public function test_empty_state_renders_when_no_news_exists(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('No news created yet.', false)
            ->assertSee('Create your first draft and submit it for review.', false)
            ->assertSee('admin-news-empty', false);
    }

    public function test_news_actions_do_not_use_fake_hash_links(): void
    {
        $category = $this->makeNewsCategory('Press');
        $author = $this->demoUser('journalist@morocco2030.test');
        $news = $this->makeNews($author, $category, ['status' => 'draft']);

        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        $this->assertStringNotContainsString('href="#"', $module);
        $this->assertStringContainsString(route('admin.news.show', $news), $module);
    }

    public function test_journalist_can_create_draft_news(): void
    {
        $category = $this->makeNewsCategory('Tournament');

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Journalist Draft Story',
                'slug' => 'journalist-draft-story',
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('news', [
            'slug' => 'journalist-draft-story',
            'status' => 'draft',
        ]);
    }

    public function test_journalist_cannot_publish_directly(): void
    {
        $category = $this->makeNewsCategory('Editorial');
        $journalist = $this->demoUser('journalist@morocco2030.test');
        $news = $this->makeNews($journalist, $category, ['status' => 'approved']);

        $this->actingAs($journalist)
            ->post(route('admin.news.publish', $news))
            ->assertForbidden();
    }

    public function test_journalist_cannot_edit_another_authors_news(): void
    {
        $category = $this->makeNewsCategory('Desk');
        $author = $this->demoUser('journalist@morocco2030.test');
        $otherJournalist = $this->makeStaffUser(['news.manage'], [
            'email' => 'other.journalist@morocco2030.test',
        ]);
        $news = $this->makeNews($author, $category, ['status' => 'draft']);

        $this->actingAs($otherJournalist)
            ->get(route('admin.news.edit', $news))
            ->assertForbidden();

        $this->actingAs($otherJournalist)
            ->put(route('admin.news.update', $news), [
                'category_id' => $category->id,
                'title' => 'Hijacked title',
                'slug' => $news->slug,
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
            ])
            ->assertForbidden();
    }

    public function test_chief_editor_can_publish_approved_news(): void
    {
        $category = $this->makeNewsCategory('Desk');
        $journalist = $this->demoUser('journalist@morocco2030.test');
        $news = $this->makeNews($journalist, $category, ['status' => 'approved']);

        $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->post(route('admin.news.publish', $news))
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('published', $news->fresh()->status);
    }

    public function test_public_user_cannot_post_forged_news_store(): void
    {
        $category = $this->makeNewsCategory('Blocked');

        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Forged',
                'slug' => 'forged',
                'summary' => 'x',
                'body' => 'x',
                'visibility' => 'public',
            ])
            ->assertForbidden();
    }

    public function test_workflow_actions_use_post_forms_with_csrf(): void
    {
        $category = $this->makeNewsCategory('Workflow');
        $journalist = $this->demoUser('journalist@morocco2030.test');
        $news = $this->makeNews($journalist, $category, ['status' => 'draft']);

        $html = $this->actingAs($journalist)
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        $this->assertStringContainsString(route('admin.news.submit-review', $news), $module);
        $this->assertStringContainsString('name="_token"', $module);
        $this->assertStringNotContainsString('admin-news-actions-menu', $module);
    }

    public function test_index_does_not_show_role_assignment_markup(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('name="role_ids[]"', $html);
    }

    private function extractNewsModule(string $html): string
    {
        if (preg_match('/<section class="panel admin-news-page.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Admin news module markup was not found.');
        }

        return $matches[0];
    }
}
