<?php

namespace Tests\Feature\Admin;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminNewsMediaUploadTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
        Storage::fake('public');
    }

    public function test_authorized_user_can_create_news_with_cover_image(): void
    {
        $category = $this->makeNewsCategory('Media Desk');

        $response = $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Cover Story',
                'slug' => 'cover-story',
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'cover_image' => UploadedFile::fake()->image('cover.jpg', 1200, 800)->size(512),
                'media_alt_text' => 'Stadium crowd',
            ]);

        $news = News::query()->where('slug', 'cover-story')->firstOrFail();
        $response->assertRedirect(route('admin.news.show', $news));

        $this->assertNotNull($news->cover_image_path);
        $this->assertStringStartsWith('news/covers/', $news->cover_image_path);
        Storage::disk('public')->assertExists($news->cover_image_path);
        $this->assertSame('Stadium crowd', $news->media_alt_text);
    }

    public function test_authorized_user_can_replace_cover_image_and_remove_old_file(): void
    {
        $category = $this->makeNewsCategory('Desk');
        $journalist = $this->demoUser('journalist@morocco2030.test');

        $this->actingAs($journalist)->post(route('admin.news.store'), [
            'category_id' => $category->id,
            'title' => 'Replace Cover',
            'slug' => 'replace-cover',
            'summary' => 'Summary',
            'body' => 'Body',
            'visibility' => 'public',
            'cover_image' => UploadedFile::fake()->image('old.jpg')->size(256),
        ]);

        $news = News::query()->where('slug', 'replace-cover')->firstOrFail();
        $oldPath = $news->cover_image_path;

        $this->actingAs($journalist)->put(route('admin.news.update', $news), [
            'category_id' => $category->id,
            'title' => 'Replace Cover',
            'slug' => 'replace-cover',
            'summary' => 'Summary',
            'body' => 'Body',
            'visibility' => 'public',
            'cover_image' => UploadedFile::fake()->image('new.jpg')->size(256),
        ]);

        $news->refresh();
        $this->assertNotSame($oldPath, $news->cover_image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($news->cover_image_path);
    }

    public function test_invalid_cover_file_type_is_rejected(): void
    {
        $category = $this->makeNewsCategory('Desk');

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->from(route('admin.news.create'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Bad File',
                'slug' => 'bad-file',
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'cover_image' => UploadedFile::fake()->create('bad.svg', 10, 'image/svg+xml'),
            ])
            ->assertRedirect(route('admin.news.create'))
            ->assertSessionHasErrors('cover_image');

        $this->assertDatabaseMissing('news', ['slug' => 'bad-file']);
    }

    public function test_oversized_cover_image_is_rejected(): void
    {
        $category = $this->makeNewsCategory('Desk');

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->from(route('admin.news.create'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Huge File',
                'slug' => 'huge-file',
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'cover_image' => UploadedFile::fake()->image('huge.jpg')->size(5000),
            ])
            ->assertRedirect(route('admin.news.create'))
            ->assertSessionHasErrors('cover_image');
    }

    public function test_gallery_upload_and_removal_work_on_update(): void
    {
        $category = $this->makeNewsCategory('Gallery');
        $journalist = $this->demoUser('journalist@morocco2030.test');

        $this->actingAs($journalist)->post(route('admin.news.store'), [
            'category_id' => $category->id,
            'title' => 'Gallery Story',
            'slug' => 'gallery-story',
            'summary' => 'Summary',
            'body' => 'Body',
            'visibility' => 'public',
            'gallery_images' => [
                UploadedFile::fake()->image('one.jpg')->size(200),
                UploadedFile::fake()->image('two.jpg')->size(200),
            ],
        ]);

        $news = News::query()->where('slug', 'gallery-story')->firstOrFail();
        $this->assertCount(2, $news->gallery_image_paths);
        $firstPath = $news->gallery_image_paths[0];

        $this->actingAs($journalist)->put(route('admin.news.update', $news), [
            'category_id' => $category->id,
            'title' => 'Gallery Story',
            'slug' => 'gallery-story',
            'summary' => 'Summary',
            'body' => 'Body',
            'visibility' => 'public',
            'remove_gallery_paths' => [$firstPath],
            'gallery_images' => [
                UploadedFile::fake()->image('three.jpg')->size(200),
            ],
        ]);

        $news->refresh();
        $this->assertCount(2, $news->gallery_image_paths);
        $this->assertNotContains($firstPath, $news->gallery_image_paths);
        Storage::disk('public')->assertMissing($firstPath);
    }

    public function test_gallery_limit_max_eight_is_enforced(): void
    {
        $category = $this->makeNewsCategory('Gallery');
        $journalist = $this->demoUser('journalist@morocco2030.test');
        $news = $this->makeNews($journalist, $category);
        $news->update([
            'gallery_image_paths' => array_map(
                fn (int $index) => "news/gallery/existing-{$index}.jpg",
                range(1, 8)
            ),
        ]);

        $response = $this->actingAs($journalist)
            ->from(route('admin.news.edit', $news))
            ->put(route('admin.news.update', $news), [
                'category_id' => $category->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'gallery_images' => [
                    UploadedFile::fake()->image('extra.jpg')->size(200),
                ],
            ]);

        $response->assertSessionHasErrors('gallery_images');
        $this->assertCount(8, $news->fresh()->gallery_image_paths);
    }

    public function test_video_url_can_be_saved_and_invalid_url_rejected(): void
    {
        $category = $this->makeNewsCategory('Video');

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Video Story',
                'slug' => 'video-story',
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertRedirect();

        $news = News::query()->where('slug', 'video-story')->firstOrFail();
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $news->video_url);

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->from(route('admin.news.create'))
            ->post(route('admin.news.store'), [
                'category_id' => $category->id,
                'title' => 'Bad Video',
                'slug' => 'bad-video',
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'video_url' => 'not-a-url',
            ])
            ->assertRedirect(route('admin.news.create'))
            ->assertSessionHasErrors('video_url');
    }

    public function test_journalist_cannot_upload_media_for_another_authors_news(): void
    {
        $category = $this->makeNewsCategory('Desk');
        $author = $this->demoUser('journalist@morocco2030.test');
        $otherJournalist = $this->makeStaffUser(['news.manage'], [
            'email' => 'other.media.journalist@morocco2030.test',
        ]);
        $news = $this->makeNews($author, $category, ['status' => 'draft']);

        $this->actingAs($otherJournalist)
            ->put(route('admin.news.update', $news), [
                'category_id' => $category->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'summary' => 'Summary',
                'body' => 'Body',
                'visibility' => 'public',
                'cover_image' => UploadedFile::fake()->image('blocked.jpg')->size(200),
            ])
            ->assertForbidden();
    }

    public function test_public_user_cannot_access_admin_news_create_form(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.news.create'))
            ->assertForbidden();
    }

    public function test_admin_news_forms_include_multipart_and_news_media_section(): void
    {
        $category = $this->makeNewsCategory('UI');
        $journalist = $this->demoUser('journalist@morocco2030.test');
        $news = $this->makeNews($journalist, $category);

        $createHtml = $this->actingAs($journalist)
            ->get(route('admin.news.create'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('enctype="multipart/form-data"', $createHtml);
        $this->assertStringContainsString('News Media', $createHtml);

        $editHtml = $this->actingAs($journalist)
            ->get(route('admin.news.edit', $news))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('enctype="multipart/form-data"', $editHtml);
        $this->assertStringContainsString('News Media', $editHtml);
    }

    public function test_media_review_index_does_not_link_generic_upload(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('Upload Image', $html);
        $this->assertStringNotContainsString(route('admin.media-files.create'), $html);
        $this->assertStringContainsString('Contextual uploads only', $html);
    }
}
