<?php

namespace Tests\Feature\Admin;

use App\Models\MediaFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class AdminMediaReviewCenterTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_sidebar_shows_media_review_when_route_exists(): void
    {
        $this->assertTrue(Route::has('admin.media-files.index'));

        $html = $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Media Review', $html);
        $this->assertStringNotContainsString('>Media</span>', $html);
        $this->assertStringNotContainsString('Upload Media', $html);
    }

    public function test_dashboard_shows_content_assets_review_not_media_inventory(): void
    {
        $html = $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Content Assets Review', $html);
        $this->assertStringContainsString('Open Media Review', $html);
        $this->assertStringNotContainsString('Media Inventory', $html);
        $this->assertStringNotContainsString('View all media', $html);
    }

    public function test_topbar_search_includes_media_review_label(): void
    {
        $html = $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Media Review', $html);
        $this->assertStringNotContainsString('Upload Media', $html);
    }

    public function test_media_review_index_explains_contextual_uploads(): void
    {
        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Media Review', false)
            ->assertSee('Review and trace visual assets linked to news, profiles, partners, cities, stadiums, teams, and players.', false)
            ->assertSee('Contextual uploads only.', false)
            ->assertSee('Generic standalone uploads are not offered from this screen.', false)
            ->assertDontSee('Upload Image', false);
    }

    public function test_media_review_index_shows_workflow_summary_labels(): void
    {
        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('News assets', false)
            ->assertSee('Profile avatars', false)
            ->assertSee('Partner logos', false)
            ->assertSee('Team visuals', false)
            ->assertSee('Player photos', false);
    }

    public function test_media_review_index_table_headers_and_empty_state(): void
    {
        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Preview', false)
            ->assertSee('Linked To', false)
            ->assertSee('Uploaded At', false)
            ->assertSee('No visual assets to review yet.', false);
    }

    public function test_media_review_index_has_no_hash_action_links(): void
    {
        $mediaFile = $this->makeMediaFile();

        $html = $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $html);
        $this->assertStringContainsString(route('admin.media-files.show', $mediaFile), $html);
    }

    public function test_news_module_navigation_still_reachable(): void
    {
        $this->actingAs($this->makeStaffUser(['news.manage']))
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('News', false);
    }

    public function test_account_profile_route_still_exists(): void
    {
        $this->assertTrue(Route::has('account.profile'));
    }

    public function test_legacy_upload_route_remains_but_not_linked_from_review_index(): void
    {
        $this->assertTrue(Route::has('admin.media-files.create'));

        $html = $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString(route('admin.media-files.create'), $html);
    }

    private function makeMediaFile(): MediaFile
    {
        return MediaFile::query()->create([
            'disk' => 'public',
            'path' => 'media/news/2030/example.webp',
            'filename' => 'example.webp',
            'original_name' => 'example-upload.webp',
            'mime_type' => 'image/webp',
            'extension' => 'webp',
            'size_bytes' => 2048,
            'checksum' => sha1('media-review-test'),
            'title' => 'Example media',
            'alt_text' => 'Example media alt text',
            'visibility' => 'public',
            'status' => 'active',
            'meta' => ['category' => 'news'],
        ]);
    }
}
