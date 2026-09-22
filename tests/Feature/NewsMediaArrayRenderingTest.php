<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class NewsMediaArrayRenderingTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_homepage_loads_when_news_has_gallery_paths_array(): void
    {
        $this->seedPublishedNewsWithGallery();

        $response = $this->get(route('home'));

        $response->assertOk();
        $this->assertDoesNotRenderRawPhpArray($response->getContent());
        $response->assertSee('gallery-render-test', false);
    }

    public function test_public_news_show_loads_with_gallery_paths_array(): void
    {
        $news = $this->seedPublishedNewsWithGallery();

        $response = $this->get(route('news.show', $news->slug));

        $response->assertOk();
        $this->assertDoesNotRenderRawPhpArray($response->getContent());
        $response->assertSee('news/gallery/one.jpg', false);
    }

    public function test_admin_news_show_and_edit_load_with_gallery_paths_array(): void
    {
        $news = $this->seedPublishedNewsWithGallery();
        $user = $this->demoUser('superadmin@morocco2030.test');

        $show = $this->actingAs($user)->get(route('admin.news.show', $news));
        $show->assertOk();
        $this->assertDoesNotRenderRawPhpArray($show->getContent());

        $edit = $this->actingAs($user)->get(route('admin.news.edit', $news));
        $edit->assertOk();
        $this->assertDoesNotRenderRawPhpArray($edit->getContent());
    }

    public function test_media_review_loads_when_news_has_gallery_assets(): void
    {
        $this->seedPublishedNewsWithGallery();

        $response = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.media-files.index'));

        $response->assertOk();
        $this->assertDoesNotRenderRawPhpArray($response->getContent());
    }

    public function test_cover_image_url_is_string_when_path_is_malformed_array(): void
    {
        $news = new News([
            'cover_image_path' => ['news/covers/first.jpg', 'news/covers/second.jpg'],
        ]);

        $url = $news->coverImageUrl();

        $this->assertIsString($url);
        $this->assertStringContainsString('news/covers/first.jpg', $url);
    }

    private function assertDoesNotRenderRawPhpArray(string $html): void
    {
        $this->assertStringNotContainsString('Array (', $html);
        $this->assertStringNotContainsString('array (', $html);
    }

    private function seedPublishedNewsWithGallery(): News
    {
        $news = $this->makeNewsWithGallery();
        $news->update([
            'status' => 'published',
            'published_at' => now()->subDay(),
            'visibility' => 'public',
        ]);

        return $news->fresh();
    }

    private function makeNewsWithGallery(): News
    {
        $author = User::factory()->create(['user_type' => 'staff', 'status' => 'active']);
        $category = $this->makeNewsCategory('Media');

        $news = $this->makeNews($author, $category, [
            'title' => 'Gallery Render Test',
            'slug' => 'gallery-render-test',
        ]);

        $news->update([
            'cover_image_path' => 'news/covers/gallery-render-test.jpg',
            'gallery_image_paths' => [
                'news/gallery/one.jpg',
                'news/gallery/two.jpg',
            ],
            'video_url' => 'https://www.youtube.com/watch?v=test123',
        ]);

        return $news->fresh();
    }
}
