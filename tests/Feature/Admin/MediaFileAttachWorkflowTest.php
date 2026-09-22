<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Models\Team;
use App\Support\PublicMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaFileAttachWorkflowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_attach_route_is_protected_and_permission_controlled(): void
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile();
        $payload = [
            'target_ref' => 'team:'.$team->id,
            'role' => 'logo',
        ];

        $this->post(route('admin.media-files.attach', $mediaFile), $payload)
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->post(route('admin.media-files.attach', $mediaFile), $payload)
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->post(route('admin.media-files.attach', $mediaFile), $payload)
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['teams.manage']))
            ->post(route('admin.media-files.attach', $mediaFile), $payload)
            ->assertForbidden();

        $this->assertSame(0, MediaRelation::query()->count());
    }

    public function test_authorized_staff_can_attach_media_to_team(): void
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile([
            'filename' => 'morocco-logo.webp',
            'path' => 'media/teams/2030/morocco-logo.webp',
            'alt_text' => 'Morocco team logo',
        ]);
        $user = $this->makeStaffUser(['media.manage', 'teams.manage']);

        $this->actingAs($user)
            ->post(route('admin.media-files.attach', $mediaFile), [
                'target_ref' => 'team:'.$team->id,
                'role' => 'logo',
            ])
            ->assertRedirect(route('admin.media-files.show', $mediaFile));

        $this->assertDatabaseHas('media_relations', [
            'media_file_id' => $mediaFile->id,
            'mediable_type' => Team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        $this->assertSame($mediaFile->id, PublicMedia::primaryFile($team->fresh(), 'logo')?->id);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => MediaFile::class,
            'auditable_id' => $mediaFile->id,
            'action' => 'media_attached',
        ]);

        $auditLog = AuditLog::query()->where('action', 'media_attached')->firstOrFail();
        $this->assertSame($mediaFile->id, $auditLog->new_values['media_file_id']);
        $this->assertSame('team', $auditLog->new_values['target_type']);
        $this->assertSame($team->id, $auditLog->new_values['target_id']);
        $this->assertSame('logo', $auditLog->new_values['role']);
        $this->assertArrayNotHasKey('file', $auditLog->new_values);
    }

    public function test_attaching_new_primary_preserves_old_relation_as_non_primary(): void
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Atlas Lions');
        $oldMedia = $this->makeMediaFile(['filename' => 'old-logo.webp', 'path' => 'media/teams/old-logo.webp']);
        $newMedia = $this->makeMediaFile(['filename' => 'new-logo.webp', 'path' => 'media/teams/new-logo.webp']);

        $oldRelation = MediaRelation::query()->create([
            'media_file_id' => $oldMedia->id,
            'mediable_type' => Team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        $this->actingAs($this->makeStaffUser(['media.manage', 'teams.manage']))
            ->post(route('admin.media-files.attach', $newMedia), [
                'target_ref' => 'team:'.$team->id,
                'role' => 'logo',
            ])
            ->assertRedirect(route('admin.media-files.show', $newMedia));

        $this->assertFalse($oldRelation->fresh()->is_primary);
        $this->assertDatabaseHas('media_relations', [
            'media_file_id' => $newMedia->id,
            'mediable_type' => Team::class,
            'mediable_id' => $team->id,
            'role' => 'logo',
            'is_primary' => true,
        ]);
        $this->assertSame(2, MediaFile::query()->count());
        $this->assertSame(2, MediaRelation::query()->where('mediable_type', Team::class)->where('mediable_id', $team->id)->count());
    }

    public function test_attach_validation_uses_strict_target_and_role_allowlists(): void
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile();
        $user = $this->makeStaffUser(['media.manage', 'teams.manage']);

        $this->actingAs($user)
            ->from(route('admin.media-files.show', $mediaFile))
            ->post(route('admin.media-files.attach', $mediaFile), [
                'target_type' => 'unsafe',
                'target_id' => $team->id,
                'role' => 'logo',
            ])
            ->assertRedirect(route('admin.media-files.show', $mediaFile))
            ->assertSessionHasErrors('target_type');

        $this->actingAs($user)
            ->from(route('admin.media-files.show', $mediaFile))
            ->post(route('admin.media-files.attach', $mediaFile), [
                'target_type' => 'team',
                'target_id' => 9999,
                'role' => 'logo',
            ])
            ->assertRedirect(route('admin.media-files.show', $mediaFile))
            ->assertSessionHasErrors('target_id');

        $this->actingAs($user)
            ->from(route('admin.media-files.show', $mediaFile))
            ->post(route('admin.media-files.attach', $mediaFile), [
                'target_type' => 'team',
                'target_id' => $team->id,
                'role' => 'photo',
            ])
            ->assertRedirect(route('admin.media-files.show', $mediaFile))
            ->assertSessionHasErrors('role');

        $this->assertSame(0, MediaRelation::query()->count());
    }

    public function test_media_file_must_exist_and_be_attachable_image(): void
    {
        $group = $this->makeGroup();
        $team = $this->makeTeam($group, 'Morocco');
        $document = $this->makeMediaFile([
            'filename' => 'document.pdf',
            'path' => 'media/documents/document.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
        ]);
        $user = $this->makeStaffUser(['media.manage', 'teams.manage']);

        $this->actingAs($user)
            ->from(route('admin.media-files.show', $document))
            ->post(route('admin.media-files.attach', $document), [
                'target_ref' => 'team:'.$team->id,
                'role' => 'logo',
            ])
            ->assertRedirect(route('admin.media-files.show', $document))
            ->assertSessionHasErrors('media_file');

        $this->actingAs($user)
            ->post('/admin/media-files/9999/attach', [
                'target_ref' => 'team:'.$team->id,
                'role' => 'logo',
            ])
            ->assertNotFound();

        $this->assertSame(0, MediaRelation::query()->count());
        $this->assertSame(1, MediaFile::query()->count());
    }

    public function test_media_show_page_renders_attach_form_for_authorized_staff(): void
    {
        $group = $this->makeGroup();
        $this->makeTeam($group, 'Morocco');
        $mediaFile = $this->makeMediaFile(['meta' => ['category' => 'teams']]);

        $this->actingAs($this->makeStaffUser(['media.manage', 'teams.manage']))
            ->get(route('admin.media-files.show', $mediaFile))
            ->assertOk()
            ->assertSee('Attach to content')
            ->assertSee('Morocco')
            ->assertSee('team logo', false)
            ->assertSee(route('admin.media-files.attach', $mediaFile), false);
    }

    public function test_no_delete_replace_routes_exist_for_media_attachment_mvp(): void
    {
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeMediaFile(array $attributes = []): MediaFile
    {
        return MediaFile::query()->create([
            'disk' => $attributes['disk'] ?? 'public',
            'path' => $attributes['path'] ?? 'media/news/2030/example.webp',
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
