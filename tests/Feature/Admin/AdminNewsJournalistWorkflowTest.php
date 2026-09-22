<?php

namespace Tests\Feature\Admin;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminNewsJournalistWorkflowTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_journalist_can_access_news_index(): void
    {
        $this->actingAs($this->journalist())
            ->get(route('admin.news.index'))
            ->assertOk();
    }

    public function test_journalist_sees_news_workspace_cta_and_workflow_explanation(): void
    {
        $this->actingAs($this->journalist())
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('News', false)
            ->assertSee('Create News', false)
            ->assertSee('Create drafts, attach media, and submit stories for editorial review.', false)
            ->assertSee('Your workflow:', false)
            ->assertSee('Draft', false)
            ->assertSee('Submit for review', false)
            ->assertSee('Editorial approval', false)
            ->assertSee('Published', false);
    }

    public function test_journalist_can_create_a_draft_news(): void
    {
        $category = $this->makeNewsCategory('Journalist Desk');

        $response = $this->actingAs($this->journalist())
            ->post(route('admin.news.store'), $this->validNewsPayload($category->id, [
                'title' => 'Journalist Match Preview',
                'slug' => 'journalist-match-preview',
            ]));

        $news = News::query()->where('slug', 'journalist-match-preview')->firstOrFail();

        $response->assertRedirect(route('admin.news.show', $news));
        $this->assertSame('draft', $news->status);
        $this->assertSame($this->journalist()->id, $news->author_id);
    }

    public function test_journalist_cannot_create_news_as_published_by_tampering_request_status(): void
    {
        $category = $this->makeNewsCategory('Tamper Desk');

        $this->actingAs($this->journalist())
            ->post(route('admin.news.store'), [
                ...$this->validNewsPayload($category->id, [
                    'title' => 'Tampered Publish Attempt',
                    'slug' => 'tampered-publish-attempt',
                ]),
                'status' => 'published',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('news', [
            'slug' => 'tampered-publish-attempt',
            'status' => 'draft',
        ]);
    }

    public function test_journalist_can_upload_media_when_creating_news(): void
    {
        Storage::fake('public');
        $category = $this->makeNewsCategory('Media Desk');

        $this->actingAs($this->journalist())
            ->post(route('admin.news.store'), [
                ...$this->validNewsPayload($category->id, [
                    'title' => 'Journalist Media Story',
                    'slug' => 'journalist-media-story',
                ]),
                'cover_image' => UploadedFile::fake()->image('cover.jpg')->size(300),
                'media_alt_text' => 'Players training before kickoff',
                'video_url' => 'https://www.youtube.com/watch?v=abc123',
            ])
            ->assertRedirect();

        $news = News::query()->where('slug', 'journalist-media-story')->firstOrFail();

        $this->assertStringStartsWith('news/covers/', $news->cover_image_path);
        $this->assertSame('Players training before kickoff', $news->media_alt_text);
        $this->assertSame('https://www.youtube.com/watch?v=abc123', $news->video_url);
        Storage::disk('public')->assertExists($news->cover_image_path);
    }

    public function test_journalist_can_submit_own_draft_for_review(): void
    {
        $news = $this->makeOwnNews(['status' => 'draft']);

        $this->actingAs($this->journalist())
            ->post(route('admin.news.submit-review', $news))
            ->assertRedirect(route('admin.news.show', $news));

        $this->assertSame('pending_review', $news->fresh()->status);
    }

    public function test_journalist_cannot_approve_reject_or_publish_news(): void
    {
        $pending = $this->makeOwnNews(['status' => 'pending_review']);
        $approved = $this->makeOwnNews([
            'title' => 'Approved Journalist Story',
            'slug' => 'approved-journalist-story',
            'status' => 'approved',
        ]);

        $this->actingAs($this->journalist())
            ->post(route('admin.news.approve', $pending))
            ->assertForbidden();

        $this->actingAs($this->journalist())
            ->post(route('admin.news.reject', $pending))
            ->assertForbidden();

        $this->actingAs($this->journalist())
            ->post(route('admin.news.publish', $approved))
            ->assertForbidden();
    }

    public function test_journalist_cannot_edit_another_journalists_news(): void
    {
        $category = $this->makeNewsCategory('Ownership Desk');
        $otherJournalist = $this->makeStaffUser(['news.manage'], [
            'email' => 'workflow.other.journalist@morocco2030.test',
        ]);
        $news = $this->makeNews($otherJournalist, $category, ['status' => 'draft']);

        $this->actingAs($this->journalist())
            ->get(route('admin.news.edit', $news))
            ->assertForbidden();

        $this->actingAs($this->journalist())
            ->put(route('admin.news.update', $news), $this->validNewsPayload($category->id, [
                'title' => 'Unauthorized Edit',
                'slug' => $news->slug,
            ]))
            ->assertForbidden();
    }

    public function test_journalist_index_does_not_show_editorial_action_buttons(): void
    {
        $news = $this->makeOwnNews(['status' => 'pending_review']);

        $html = $this->actingAs($this->journalist())
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractNewsModule($html);

        $this->assertStringContainsString(route('admin.news.show', $news), $module);
        $this->assertStringNotContainsString(route('admin.news.approve', $news), $module);
        $this->assertStringNotContainsString(route('admin.news.reject', $news), $module);
        $this->assertStringNotContainsString(route('admin.news.publish', $news), $module);
        $this->assertStringNotContainsString('>Approve<', $module);
        $this->assertStringNotContainsString('>Reject<', $module);
        $this->assertStringNotContainsString('>Publish<', $module);
    }

    public function test_journalist_draft_and_pending_review_news_do_not_appear_publicly(): void
    {
        $draft = $this->makeOwnNews([
            'title' => 'Hidden Draft Story',
            'slug' => 'hidden-draft-story',
            'status' => 'draft',
        ]);
        $pending = $this->makeOwnNews([
            'title' => 'Hidden Pending Story',
            'slug' => 'hidden-pending-story',
            'status' => 'pending_review',
        ]);

        $this->get(route('news.index'))
            ->assertOk()
            ->assertDontSee($draft->title, false)
            ->assertDontSee($pending->title, false);

        $this->get(route('news.show', $draft->slug))->assertNotFound();
        $this->get(route('news.show', $pending->slug))->assertNotFound();
    }

    public function test_published_news_appears_publicly_after_authorized_publish_action(): void
    {
        $news = $this->makeOwnNews([
            'title' => 'Published Journalist Story',
            'slug' => 'published-journalist-story',
            'status' => 'approved',
        ]);

        $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->post(route('admin.news.publish', $news))
            ->assertRedirect(route('admin.news.show', $news));

        $news->refresh();

        $this->assertSame('published', $news->status);

        $this->get(route('news.index'))
            ->assertOk()
            ->assertSee('Published Journalist Story', false);

        $this->get(route('news.show', $news->slug))
            ->assertOk()
            ->assertSee('Published Journalist Story', false);
    }

    public function test_news_index_contains_no_fake_hash_links(): void
    {
        $this->makeOwnNews(['status' => 'draft']);

        $html = $this->actingAs($this->journalist())
            ->get(route('admin.news.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $this->extractNewsModule($html));
    }

    public function test_create_and_edit_forms_use_multipart_when_media_fields_exist(): void
    {
        $news = $this->makeOwnNews(['status' => 'draft']);

        $this->actingAs($this->journalist())
            ->get(route('admin.news.create'))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('News Media', false)
            ->assertSee('Save a draft first. You can submit it for editorial review after it exists.', false);

        $this->actingAs($this->journalist())
            ->get(route('admin.news.edit', $news))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('News Media', false);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeOwnNews(array $attributes = []): News
    {
        return $this->makeNews(
            $this->journalist(),
            $this->makeNewsCategory($attributes['category'] ?? 'Workflow Desk'),
            $attributes
        );
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validNewsPayload(int $categoryId, array $overrides = []): array
    {
        return [
            'category_id' => $categoryId,
            'title' => 'Journalist Draft',
            'slug' => 'journalist-draft',
            'summary' => 'Short summary for the editorial desk.',
            'body' => 'Longer body for the editorial desk.',
            'visibility' => 'public',
            ...$overrides,
        ];
    }

    private function journalist(): \App\Models\User
    {
        return $this->demoUser('journalist@morocco2030.test');
    }

    private function extractNewsModule(string $html): string
    {
        if (preg_match('/<section class="[^"]*admin-news-page[^"]*"[^>]*>.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Admin news module markup was not found.');
        }

        return $matches[0];
    }
}
