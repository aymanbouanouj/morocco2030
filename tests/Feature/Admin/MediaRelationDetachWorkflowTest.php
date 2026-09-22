<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaRelationDetachWorkflowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_detach_route_is_protected_and_permission_controlled(): void
    {
        [$mediaFile, $relation] = $this->makeAttachedTeamMedia();

        $this->delete(route('admin.media-relations.destroy', $relation))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->delete(route('admin.media-relations.destroy', $relation))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->delete(route('admin.media-relations.destroy', $relation))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['teams.manage']))
            ->delete(route('admin.media-relations.destroy', $relation))
            ->assertForbidden();

        $this->assertSame(1, MediaRelation::query()->whereKey($relation->id)->count());
        $this->assertSame(1, MediaFile::query()->whereKey($mediaFile->id)->count());
    }

    public function test_authorized_staff_can_detach_media_relation_without_deleting_media_or_file(): void
    {
        Storage::fake('public');

        [$mediaFile, $relation] = $this->makeAttachedTeamMedia([
            'path' => 'media/teams/2030/morocco-logo.webp',
        ]);
        Storage::disk('public')->put($mediaFile->path, 'fake-webp-bytes');

        $this->actingAs($this->makeStaffUser(['media.manage', 'teams.manage']))
            ->delete(route('admin.media-relations.destroy', $relation))
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $this->assertSame(0, MediaRelation::query()->whereKey($relation->id)->count());
        $this->assertSame(1, MediaFile::query()->whereKey($mediaFile->id)->count());
        Storage::disk('public')->assertExists($mediaFile->path);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => MediaFile::class,
            'auditable_id' => $mediaFile->id,
            'action' => 'media_detached',
        ]);

        $auditLog = AuditLog::query()->where('action', 'media_detached')->firstOrFail();
        $this->assertSame($mediaFile->id, $auditLog->old_values['media_file_id']);
        $this->assertSame($relation->id, $auditLog->old_values['media_relation_id']);
        $this->assertSame(Team::class, $auditLog->old_values['target_type']);
        $this->assertSame('logo', $auditLog->old_values['role']);
        $this->assertTrue($auditLog->old_values['was_primary']);
        $this->assertNull($auditLog->new_values);
    }

    public function test_detach_removes_only_selected_relation(): void
    {
        $group = $this->makeGroup();
        $firstTeam = $this->makeTeam($group, 'Morocco');
        $secondTeam = $this->makeTeam($group, 'Portugal');
        $mediaFile = $this->makeMediaFile();
        $firstRelation = $this->makeRelation($mediaFile, $firstTeam);
        $secondRelation = $this->makeRelation($mediaFile, $secondTeam);

        $this->actingAs($this->makeStaffUser(['media.manage', 'teams.manage']))
            ->delete(route('admin.media-relations.destroy', $firstRelation))
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $this->assertSame(0, MediaRelation::query()->whereKey($firstRelation->id)->count());
        $this->assertSame(1, MediaRelation::query()->whereKey($secondRelation->id)->count());
        $this->assertSame(1, MediaFile::query()->whereKey($mediaFile->id)->count());
    }

    public function test_unknown_relation_returns_not_found(): void
    {
        $this->actingAs($this->makeStaffUser(['media.manage', 'teams.manage']))
            ->delete('/admin/media-relations/9999')
            ->assertNotFound();
    }

    public function test_media_show_page_renders_detach_button_for_authorized_staff(): void
    {
        [$mediaFile, $relation] = $this->makeAttachedTeamMedia();

        $this->actingAs($this->makeStaffUser(['media.manage', 'teams.manage']))
            ->get(route('admin.media-files.show', $mediaFile))
            ->assertOk()
            ->assertSee('Detach')
            ->assertSee('File is not deleted')
            ->assertSee(route('admin.media-relations.destroy', $relation), false);
    }

    public function test_no_media_delete_replace_archive_routes_are_added(): void
    {
        $this->assertTrue(Route::has('admin.media-relations.destroy'));
        $this->assertTrue(Route::has('admin.media-files.attach'));
        $this->assertTrue(Route::has('admin.media-files.archive'));
        $this->assertTrue(Route::has('admin.media-files.restore'));
        $this->assertTrue(Route::has('admin.media-files.edit'));
        $this->assertTrue(Route::has('admin.media-files.update'));
        $this->assertFalse(Route::has('admin.media-files.destroy'));
        $this->assertFalse(Route::has('admin.media-files.replace'));
    }

    /**
     * @param  array<string, mixed>  $mediaAttributes
     * @return array{0: MediaFile, 1: MediaRelation}
     */
    private function makeAttachedTeamMedia(array $mediaAttributes = []): array
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile($mediaAttributes);

        return [$mediaFile, $this->makeRelation($mediaFile, $team)];
    }

    private function makeRelation(MediaFile $mediaFile, Team $team): MediaRelation
    {
        return MediaRelation::query()->create([
            'media_file_id' => $mediaFile->id,
            'mediable_type' => Team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'sort_order' => 0,
            'is_primary' => true,
        ]);
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
