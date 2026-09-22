<?php

namespace Tests\Feature\Admin;

use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaFilesReadOnlyAdminTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_files_index_is_protected_and_permission_controlled(): void
    {
        $mediaFile = $this->makeMediaFile();

        $this->get(route('admin.media-files.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.media-files.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.media-files.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Media Review')
            ->assertDontSee('Upload Image', false)
            ->assertSee($mediaFile->filename)
            ->assertSee('Contextual uploads only.', false)
            ->assertSee('Generic standalone uploads are not offered from this screen.', false);
    }

    public function test_authorized_staff_can_view_media_file_detail(): void
    {
        $mediaFile = $this->makeMediaFile([
            'title' => 'Casablanca stadium cover',
            'alt_text' => 'A stadium exterior',
            'caption' => 'Approved presentation image.',
        ]);

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.show', $mediaFile))
            ->assertOk()
            ->assertSee('Casablanca stadium cover')
            ->assertSee('A stadium exterior')
            ->assertSee('Approved presentation image.')
            ->assertSee('Metadata detail page');
    }

    public function test_media_file_usage_relations_are_visible_without_mutation(): void
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile([
            'filename' => 'morocco-logo.webp',
            'original_name' => 'morocco-logo.webp',
            'path' => 'media/teams/2030/morocco-logo.webp',
        ]);

        MediaRelation::query()->create([
            'media_file_id' => $mediaFile->id,
            'mediable_type' => $team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'sort_order' => 1,
            'is_primary' => true,
        ]);

        $before = $mediaFile->fresh()->toArray();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.show', $mediaFile))
            ->assertOk()
            ->assertSee('Usage relationships')
            ->assertSee('Team')
            ->assertSee('Morocco')
            ->assertSee('logo')
            ->assertSee('Yes');

        $this->assertSame($before, $mediaFile->fresh()->toArray());
        $this->assertSame(1, MediaRelation::query()->where('media_file_id', $mediaFile->id)->count());
    }

    public function test_media_files_routes_do_not_include_delete_or_replace(): void
    {
        $this->assertTrue(Route::has('admin.media-files.index'));
        $this->assertTrue(Route::has('admin.media-files.show'));
        $this->assertTrue(Route::has('admin.media-files.create'));
        $this->assertTrue(Route::has('admin.media-files.store'));
        $this->assertTrue(Route::has('admin.media-files.attach'));
        $this->assertTrue(Route::has('admin.media-relations.destroy'));
        $this->assertTrue(Route::has('admin.media-files.archive'));
        $this->assertTrue(Route::has('admin.media-files.restore'));
        $this->assertTrue(Route::has('admin.media-files.edit'));
        $this->assertTrue(Route::has('admin.media-files.update'));
        $this->assertFalse(Route::has('admin.media-files.destroy'));
        $this->assertFalse(Route::has('admin.media-files.replace'));
    }

    public function test_private_or_non_public_media_does_not_render_public_preview_url(): void
    {
        $mediaFile = $this->makeMediaFile([
            'disk' => 'private',
            'path' => 'internal/board-only.jpg',
            'visibility' => 'private',
            'status' => 'active',
            'mime_type' => 'image/jpeg',
        ]);

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.show', $mediaFile))
            ->assertOk()
            ->assertSee('Public URL hidden')
            ->assertDontSee('/storage/internal/board-only.jpg');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeMediaFile(array $attributes = []): MediaFile
    {
        return MediaFile::query()->create([
            'disk' => $attributes['disk'] ?? 'public',
            'path' => $attributes['path'] ?? 'media/news/2030/example.webp',
            'filename' => $attributes['filename'] ?? 'example.webp',
            'original_name' => $attributes['original_name'] ?? 'example.webp',
            'mime_type' => $attributes['mime_type'] ?? 'image/webp',
            'extension' => $attributes['extension'] ?? 'webp',
            'size_bytes' => $attributes['size_bytes'] ?? 2048,
            'width' => $attributes['width'] ?? 1200,
            'height' => $attributes['height'] ?? 800,
            'checksum' => $attributes['checksum'] ?? sha1(uniqid('media', true)),
            'title' => $attributes['title'] ?? 'Example media',
            'alt_text' => $attributes['alt_text'] ?? 'Example media alt text',
            'caption' => $attributes['caption'] ?? null,
            'visibility' => $attributes['visibility'] ?? 'public',
            'status' => $attributes['status'] ?? 'active',
            'meta' => $attributes['meta'] ?? ['source' => 'feature-test'],
        ]);
    }
}
