<?php

namespace Tests\Feature\Admin;

use App\Models\EditorialWorkflow;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class NewsWorkflowTransitionsTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_news_can_move_through_submit_approve_publish_and_archive_transitions(): void
    {
        $journalist = $this->makeStaffUser(['news.manage']);
        $chiefEditor = $this->makeStaffUser(['news.manage', 'news.review', 'news.publish']);
        $category = $this->makeNewsCategory('Tournament Updates');

        $this->actingAs($journalist)
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Atlas Lions Prepare for Kickoff',
                'slug' => 'atlas-lions-prepare-for-kickoff',
                'summary' => 'Preparation summary.',
                'body' => 'Detailed body.',
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $news = News::query()->firstOrFail();

        $this->assertSame('draft', $news->status);
        $this->assertDatabaseHas('editorial_workflows', [
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'current_step' => 'created',
            'status' => 'draft',
        ]);

        $this->actingAs($journalist)
            ->post(route('admin.news.submit-review', $news), [
                'notes' => 'Ready for editorial review.',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('pending_review', $news->fresh()->status);

        $this->actingAs($journalist)
            ->post(route('admin.news.approve', $news))
            ->assertForbidden();

        $this->actingAs($chiefEditor)
            ->post(route('admin.news.approve', $news), [
                'notes' => 'Approved for publication.',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('approved', $news->fresh()->status);
        $this->assertSame($chiefEditor->id, $news->fresh()->editor_id);

        $this->actingAs($chiefEditor)
            ->post(route('admin.news.publish', $news), [
                'notes' => 'Publishing now.',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $publishedNews = $news->fresh();

        $this->assertSame('published', $publishedNews->status);
        $this->assertNotNull($publishedNews->published_at);

        $this->actingAs($chiefEditor)
            ->post(route('admin.news.archive', $news), [
                'notes' => 'Archiving after publication.',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $archivedNews = $news->fresh();

        $this->assertSame('archived', $archivedNews->status);
        $this->assertNull($archivedNews->published_at);

        $this->assertDatabaseHas('editorial_workflows', [
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'current_step' => 'submit_for_review',
            'status' => 'pending_review',
        ]);

        $this->assertDatabaseHas('editorial_workflows', [
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'current_step' => 'approve',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('editorial_workflows', [
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'current_step' => 'publish',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('editorial_workflows', [
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'current_step' => 'archive',
            'status' => 'archived',
        ]);
    }

    public function test_rejected_news_can_be_edited_again_by_its_author(): void
    {
        $journalist = $this->makeStaffUser(['news.manage']);
        $chiefEditor = $this->makeStaffUser(['news.manage', 'news.review', 'news.publish']);
        $category = $this->makeNewsCategory('Press Releases');
        $news = $this->makeNews($journalist, $category, [
            'title' => 'Initial Draft',
            'slug' => 'initial-draft',
        ]);

        EditorialWorkflow::query()->create([
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'submitted_by' => $journalist->id,
            'status' => 'draft',
            'current_step' => 'created',
            'payload' => ['new_status' => 'draft'],
        ]);

        $this->actingAs($journalist)
            ->post(route('admin.news.submit-review', $news), [
                'notes' => 'Please review.',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $this->actingAs($chiefEditor)
            ->post(route('admin.news.reject', $news), [
                'notes' => 'Needs stronger sourcing.',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('rejected', $news->fresh()->status);

        $this->actingAs($journalist)
            ->put(route('admin.news.update', $news), [
                'category_id' => $category->id,
                'title' => 'Initial Draft Revised',
                'slug' => $news->slug,
                'summary' => 'Updated summary.',
                'body' => 'Updated body with stronger sourcing.',
                'visibility' => 'public',
            ])
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Initial Draft Revised',
            'status' => 'rejected',
        ]);

        $this->assertDatabaseHas('editorial_workflows', [
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'current_step' => 'reject',
            'status' => 'rejected',
            'notes' => 'Needs stronger sourcing.',
        ]);
    }
}
