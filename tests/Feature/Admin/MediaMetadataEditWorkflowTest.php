<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaMetadataEditWorkflowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_metadata_edit_routes_are_protected_and_permission_controlled(): void
    {
        $mediaFile = $this->makeMediaFile();

        $this->get(route('admin.media-files.edit', $mediaFile))
            ->assertRedirect(route('login'));

        $this->patch(route('admin.media-files.update', $mediaFile), [
            'alt_text' => 'Guest edit attempt',
        ])->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.media-files.edit', $mediaFile))
            ->assertForbidden();

        $this->actingAs($this->makePublicUser())
            ->patch(route('admin.media-files.update', $mediaFile), [
                'alt_text' => 'Public user edit attempt',
            ])->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.media-files.edit', $mediaFile))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->patch(route('admin.media-files.update', $mediaFile), [
                'alt_text' => 'Unauthorized staff edit attempt',
            ])->assertForbidden();

        $this->assertSame('Example media alt text', $mediaFile->fresh()->alt_text);
    }

    public function test_authorized_staff_can_access_metadata_edit_form(): void
    {
        $mediaFile = $this->makeMediaFile();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.edit', $mediaFile))
            ->assertOk()
            ->assertSee('Metadata-only edit')
            ->assertSee('Alt text')
            ->assertSee('Caption')
            ->assertSee('Visibility')
            ->assertDontSee('<input type="file"', false)
            ->assertDontSee('name="file"', false);
    }

    public function test_authorized_staff_can_update_safe_metadata_fields(): void
    {
        $mediaFile = $this->makeMediaFile([
            'alt_text' => 'Old alt text',
            'caption' => 'Old caption.',
            'visibility' => 'public',
        ]);

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->patch(route('admin.media-files.update', $mediaFile), [
                'alt_text' => 'Updated accessible description',
                'caption' => 'Updated editorial caption.',
                'visibility' => 'private',
            ])
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $mediaFile->refresh();

        $this->assertSame('Updated accessible description', $mediaFile->alt_text);
        $this->assertSame('Updated editorial caption.', $mediaFile->caption);
        $this->assertSame('private', $mediaFile->visibility);

        $auditLog = AuditLog::query()->where('action', 'media_updated')->firstOrFail();
        $changedFields = $auditLog->old_values['changed_fields'];
        sort($changedFields);

        $this->assertSame($mediaFile->id, $auditLog->old_values['media_file_id']);
        $this->assertSame(['alt_text', 'caption', 'visibility'], $changedFields);
        $this->assertSame('Old alt text', $auditLog->old_values['values']['alt_text']);
        $this->assertSame('public', $auditLog->old_values['values']['visibility']);
        $this->assertSame('Updated accessible description', $auditLog->new_values['values']['alt_text']);
        $this->assertSame('private', $auditLog->new_values['values']['visibility']);
        $this->assertArrayNotHasKey('path', $auditLog->new_values['values']);
        $this->assertArrayNotHasKey('file', $auditLog->new_values['values']);
    }

    public function test_forbidden_fields_and_files_are_ignored_by_metadata_update(): void
    {
        Storage::fake('public');

        $mediaFile = $this->makeMediaFile([
            'disk' => 'public',
            'path' => 'media/news/2030/original.webp',
            'filename' => 'original.webp',
            'original_name' => 'original-upload.webp',
            'mime_type' => 'image/webp',
            'extension' => 'webp',
            'size_bytes' => 2048,
            'checksum' => 'original-checksum',
            'title' => 'Original title',
            'status' => 'active',
            'meta' => ['category' => 'news', 'source' => 'feature-test'],
        ]);
        Storage::disk('public')->put($mediaFile->path, 'original-bytes');

        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $relation = MediaRelation::query()->create([
            'media_file_id' => $mediaFile->id,
            'mediable_type' => Team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->patch(route('admin.media-files.update', $mediaFile), [
                'alt_text' => 'Safe alt text',
                'caption' => 'Safe caption.',
                'visibility' => 'public',
                'file' => UploadedFile::fake()->image('replacement.jpg'),
                'disk' => 's3',
                'path' => 'media/hacked/replacement.jpg',
                'filename' => 'replacement.jpg',
                'original_name' => 'replacement.jpg',
                'mime_type' => 'image/jpeg',
                'extension' => 'jpg',
                'size_bytes' => 999999,
                'checksum' => 'changed-checksum',
                'title' => 'Changed title',
                'status' => 'archived',
                'meta' => ['category' => 'partners'],
            ])
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $mediaFile->refresh();

        $this->assertSame('Safe alt text', $mediaFile->alt_text);
        $this->assertSame('Safe caption.', $mediaFile->caption);
        $this->assertSame('public', $mediaFile->visibility);
        $this->assertSame('public', $mediaFile->disk);
        $this->assertSame('media/news/2030/original.webp', $mediaFile->path);
        $this->assertSame('original.webp', $mediaFile->filename);
        $this->assertSame('original-upload.webp', $mediaFile->original_name);
        $this->assertSame('image/webp', $mediaFile->mime_type);
        $this->assertSame('webp', $mediaFile->extension);
        $this->assertSame(2048, $mediaFile->size_bytes);
        $this->assertSame('original-checksum', $mediaFile->checksum);
        $this->assertSame('Original title', $mediaFile->title);
        $this->assertSame('active', $mediaFile->status);
        $this->assertSame(['category' => 'news', 'source' => 'feature-test'], $mediaFile->meta);
        $this->assertSame(1, MediaRelation::query()->whereKey($relation->id)->count());
        $this->assertSame([$mediaFile->path], Storage::disk('public')->allFiles());
    }

    public function test_invalid_visibility_is_rejected_without_mutating_metadata(): void
    {
        $mediaFile = $this->makeMediaFile([
            'alt_text' => 'Existing alt',
            'visibility' => 'public',
        ]);

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->patch(route('admin.media-files.update', $mediaFile), [
                'alt_text' => 'Attempted alt',
                'visibility' => 'secret',
            ])
            ->assertSessionHasErrors('visibility');

        $mediaFile->refresh();

        $this->assertSame('Existing alt', $mediaFile->alt_text);
        $this->assertSame('public', $mediaFile->visibility);
        $this->assertSame(0, AuditLog::query()->where('action', 'media_updated')->count());
    }

    public function test_no_media_delete_force_delete_or_replace_routes_are_added(): void
    {
        $this->assertTrue(Route::has('admin.media-files.edit'));
        $this->assertTrue(Route::has('admin.media-files.update'));
        $this->assertFalse(Route::has('admin.media-files.destroy'));
        $this->assertFalse(Route::has('admin.media-files.force-delete'));
        $this->assertFalse(Route::has('admin.media-files.replace'));
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
