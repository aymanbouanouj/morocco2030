<?php

namespace Tests\Feature\Site;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicNewsVisibilityTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_published_news_is_public_and_draft_or_pending_news_stays_hidden(): void
    {
        $author = $this->makeStaffUser();
        $category = NewsCategory::ensureTournamentUpdatesCategory();

        $published = News::query()->create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Published Public QA Story',
            'slug' => 'published-public-qa-story',
            'summary' => 'Published summary.',
            'body' => 'Published body.',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now()->subHour(),
        ]);

        foreach (['draft' => 'Hidden Draft QA Story', 'pending_review' => 'Hidden Pending QA Story'] as $status => $title) {
            News::query()->create([
                'category_id' => $category->id,
                'author_id' => $author->id,
                'title' => $title,
                'slug' => str($title)->slug()->toString(),
                'summary' => 'Hidden summary.',
                'body' => 'Hidden body.',
                'status' => $status,
                'visibility' => 'public',
            ]);
        }

        $this->get(route('news.index'))
            ->assertOk()
            ->assertSee($published->title, false)
            ->assertDontSee('Hidden Draft QA Story', false)
            ->assertDontSee('Hidden Pending QA Story', false)
            ->assertDontSee('href="#"', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($published->title, false)
            ->assertDontSee('Hidden Draft QA Story', false)
            ->assertDontSee('Hidden Pending QA Story', false);

        $this->get(route('news.show', $published->slug))
            ->assertOk()
            ->assertSee($published->title, false);

        $this->get(route('news.show', 'hidden-draft-qa-story'))->assertNotFound();
        $this->get(route('news.show', 'hidden-pending-qa-story'))->assertNotFound();
    }
}
