<?php

namespace Tests\Feature\Admin;

use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class VenueManagerMediaReviewTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_venue_manager_can_access_media_review_without_generic_upload_or_fake_links(): void
    {
        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Media Review', false)
            ->assertSee('Contextual uploads only.', false)
            ->assertSee('Generic standalone uploads are not offered from this screen.', false)
            ->assertDontSee('Upload Image', false)
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $html);
        $this->assertStringNotContainsString(route('admin.media-files.create'), $html);
    }

    public function test_media_review_counts_render_safely_for_contextual_asset_categories(): void
    {
        $venueManager = $this->demoUser('venue.manager@morocco2030.test');
        $profileAvatar = $this->makeMediaFile('profiles', 'profile-avatar.webp');

        MediaRelation::query()->create([
            'media_file_id' => $profileAvatar->id,
            'mediable_type' => $venueManager::class,
            'mediable_id' => $venueManager->id,
            'role' => 'avatar',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        $this->makeMediaFile('news', 'news-cover.webp');
        $this->makeMediaFile('partners', 'partner-logo.webp');
        $this->makeMediaFile('cities', 'city-visual.webp');
        $this->makeMediaFile('stadiums', 'stadium-visual.webp');
        $this->makeMediaFile('teams', 'team-visual.webp');
        $this->makeMediaFile('players', 'player-photo.webp');

        $html = $this->actingAs($venueManager)
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('News assets', false)
            ->assertSee('Profile avatars', false)
            ->assertSee('Partner logos', false)
            ->assertSee('City / Stadium', false)
            ->assertSee('Team visuals', false)
            ->assertSee('Player photos', false)
            ->getContent();

        $this->assertSummaryCount($html, 'News assets', 1);
        $this->assertSummaryCount($html, 'Profile avatars', 1);
        $this->assertSummaryCount($html, 'Partner logos', 1);
        $this->assertSummaryCount($html, 'City / Stadium', 2);
        $this->assertSummaryCount($html, 'Team visuals', 1);
        $this->assertSummaryCount($html, 'Player photos', 1);
    }

    public function test_venue_manager_can_access_media_readiness_page(): void
    {
        $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.media-readiness.index'))
            ->assertOk()
            ->assertSee('Media Readiness', false)
            ->assertSee('Uploads', false)
            ->assertSee('Disabled', false);
    }

    public function test_venue_manager_cannot_access_unauthorized_admin_modules(): void
    {
        $user = $this->demoUser('venue.manager@morocco2030.test');

        foreach ([
            'admin.football-data-import.index',
            'admin.users.index',
            'admin.news.index',
            'admin.teams.index',
            'admin.players.index',
            'admin.matches.index',
            'admin.groups.index',
            'admin.settings.index',
            'admin.audit-logs.index',
        ] as $routeName) {
            $this->actingAs($user)->get(route($routeName))->assertForbidden();
        }
    }

    private function makeMediaFile(string $category, string $filename): MediaFile
    {
        return MediaFile::query()->create([
            'disk' => 'public',
            'path' => 'media/'.$category.'/'.$filename,
            'filename' => $filename,
            'original_name' => $filename,
            'mime_type' => 'image/webp',
            'extension' => 'webp',
            'size_bytes' => 2048,
            'visibility' => 'public',
            'status' => 'active',
            'meta' => ['category' => $category],
        ]);
    }

    private function assertSummaryCount(string $html, string $label, int $count): void
    {
        $this->assertMatchesRegularExpression(
            '/'.preg_quote($label, '/').'<\/span>\s*<span class="admin-media-review-source__count">'.preg_quote((string) $count, '/').'<\/span>/',
            $html
        );
    }
}
