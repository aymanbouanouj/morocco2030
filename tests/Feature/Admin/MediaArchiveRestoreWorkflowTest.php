<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Models\Team;
use App\Support\PublicMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaArchiveRestoreWorkflowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_archive_and_restore_routes_are_protected_and_permission_controlled(): void
    {
        $mediaFile = $this->makeMediaFile();

        $this->patch(route('admin.media-files.archive', $mediaFile))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->patch(route('admin.media-files.archive', $mediaFile))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->patch(route('admin.media-files.archive', $mediaFile))
            ->assertForbidden();

        $mediaFile->update(['status' => 'archived']);

        $this->actingAs($this->makeStaffUser())
            ->patch(route('admin.media-files.restore', $mediaFile))
            ->assertForbidden();

        $this->assertSame('archived', $mediaFile->fresh()->status);
    }

    public function test_authorized_staff_can_archive_media_without_deleting_records_relations_or_file(): void
    {
        Storage::fake('public');

        [$mediaFile, $relation, $team] = $this->makeAttachedTeamMedia([
            'path' => 'media/teams/2030/morocco-logo.webp',
        ]);
        Storage::disk('public')->put($mediaFile->path, 'fake-webp-bytes');

        $this->assertSame($mediaFile->id, PublicMedia::primaryFile($team->fresh(), 'logo')?->id);

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->patch(route('admin.media-files.archive', $mediaFile))
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $this->assertSame('archived', $mediaFile->fresh()->status);
        $this->assertSame(1, MediaFile::query()->whereKey($mediaFile->id)->count());
        $this->assertSame(1, MediaRelation::query()->whereKey($relation->id)->count());
        Storage::disk('public')->assertExists($mediaFile->path);
        $this->assertNull(PublicMedia::primaryFile($team->fresh(), 'logo'));

        $auditLog = AuditLog::query()->where('action', 'media_archived')->firstOrFail();
        $this->assertSame($mediaFile->id, $auditLog->old_values['media_file_id']);
        $this->assertSame('active', $auditLog->old_values['previous_status']);
        $this->assertSame($mediaFile->id, $auditLog->new_values['media_file_id']);
        $this->assertSame('archived', $auditLog->new_values['new_status']);
        $this->assertSame($mediaFile->path, $auditLog->new_values['relative_path']);
        $this->assertArrayNotHasKey('file', $auditLog->new_values);
    }

    public function test_authorized_staff_can_restore_archived_media_without_deleting_records_relations_or_file(): void
    {
        Storage::fake('public');

        [$mediaFile, $relation, $team] = $this->makeAttachedTeamMedia([
            'path' => 'media/teams/2030/archived-logo.webp',
            'status' => 'archived',
        ]);
        Storage::disk('public')->put($mediaFile->path, 'fake-webp-bytes');

        $this->assertNull(PublicMedia::primaryFile($team->fresh(), 'logo'));

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->patch(route('admin.media-files.restore', $mediaFile))
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $this->assertSame('active', $mediaFile->fresh()->status);
        $this->assertSame(1, MediaFile::query()->whereKey($mediaFile->id)->count());
        $this->assertSame(1, MediaRelation::query()->whereKey($relation->id)->count());
        Storage::disk('public')->assertExists($mediaFile->path);
        $this->assertSame($mediaFile->id, PublicMedia::primaryFile($team->fresh(), 'logo')?->id);

        $auditLog = AuditLog::query()->where('action', 'media_restored')->firstOrFail();
        $this->assertSame($mediaFile->id, $auditLog->old_values['media_file_id']);
        $this->assertSame('archived', $auditLog->old_values['previous_status']);
        $this->assertSame($mediaFile->id, $auditLog->new_values['media_file_id']);
        $this->assertSame('active', $auditLog->new_values['new_status']);
    }

    public function test_media_show_page_renders_archive_or_restore_button_by_status(): void
    {
        $activeMedia = $this->makeMediaFile(['status' => 'active']);
        $archivedMedia = $this->makeMediaFile([
            'path' => 'media/teams/2030/archived.webp',
            'filename' => 'archived.webp',
            'status' => 'archived',
        ]);
        $user = $this->makeStaffUser(['media.manage']);

        $this->actingAs($user)
            ->get(route('admin.media-files.show', $activeMedia))
            ->assertOk()
            ->assertSee('>Archive</button>', false)
            ->assertDontSee('>Restore</button>', false);

        $this->actingAs($user)
            ->get(route('admin.media-files.show', $archivedMedia))
            ->assertOk()
            ->assertSee('>Restore</button>', false)
            ->assertDontSee('>Archive</button>', false);
    }

    public function test_no_media_delete_force_delete_or_replace_routes_are_added(): void
    {
        $this->assertTrue(Route::has('admin.media-files.edit'));
        $this->assertTrue(Route::has('admin.media-files.update'));
        $this->assertTrue(Route::has('admin.media-files.archive'));
        $this->assertTrue(Route::has('admin.media-files.restore'));
        $this->assertFalse(Route::has('admin.media-files.destroy'));
        $this->assertFalse(Route::has('admin.media-files.force-delete'));
        $this->assertFalse(Route::has('admin.media-files.replace'));
    }

    /**
     * @param  array<string, mixed>  $mediaAttributes
     * @return array{0: MediaFile, 1: MediaRelation, 2: Team}
     */
    private function makeAttachedTeamMedia(array $mediaAttributes = []): array
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile($mediaAttributes);
        $relation = MediaRelation::query()->create([
            'media_file_id' => $mediaFile->id,
            'mediable_type' => Team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        return [$mediaFile, $relation, $team];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeMediaFile(array $attributes = []): MediaFile
    {
        return MediaFile::query()->create([
            'disk' => $attributes['disk'] ?? 'public',
            'path' => $attributes['path'] ?? 'media/teams/2030/example.webp',
            'filename' => $attributes['filename'] ?? 'example.webp',
            'original_name' => $attributes['original_name'] ?? ($attributes['filename'] ?? 'example.webp'),
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
