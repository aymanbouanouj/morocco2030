<?php

namespace Tests\Feature\Admin;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class NewsPublishingWorkflowTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_journalist_can_create_and_submit_news_with_ready_category_but_cannot_publish(): void
    {
        $category = NewsCategory::ensureTournamentUpdatesCategory();

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.news.store'), $this->payload($category->id))
            ->assertRedirect();

        $news = News::query()->where('slug', 'qa-news-workflow-story')->firstOrFail();
        $this->assertSame('draft', $news->status);

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.news.submit-review', $news))
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('pending_review', $news->fresh()->status);

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.news.publish', $news))
            ->assertForbidden();
    }

    public function test_chief_editor_can_approve_and_super_admin_can_publish_news(): void
    {
        $category = NewsCategory::ensureTournamentUpdatesCategory();
        $news = News::query()->create([
            ...$this->payload($category->id, 'qa-chief-editor-publish-story'),
            'author_id' => $this->demoUser('journalist@morocco2030.test')->id,
            'status' => 'pending_review',
        ]);

        $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->post(route('admin.news.approve', $news))
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('approved', $news->fresh()->status);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.news.publish', $news))
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('published', $news->fresh()->status);
        $this->assertNotNull($news->fresh()->published_at);
    }

    private function payload(int $categoryId, string $slug = 'qa-news-workflow-story'): array
    {
        return [
            'category_id' => $categoryId,
            'title' => 'QA News Workflow Story',
            'slug' => $slug,
            'summary' => 'Workflow summary.',
            'body' => 'Workflow body.',
            'visibility' => 'public',
        ];
    }
}
