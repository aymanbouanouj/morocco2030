<?php

namespace Tests\Feature\Admin;

use App\Models\MediaFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaReadinessAdminModuleTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_readiness_admin_route_is_protected_and_permission_controlled(): void
    {
        $this->get(route('admin.media-readiness.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.media-readiness.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.media-readiness.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-readiness.index'))
            ->assertOk()
            ->assertSee('Media Readiness')
            ->assertSee('Uploads')
            ->assertSee('Disabled');
    }

    public function test_media_readiness_page_reports_asset_and_placeholder_status(): void
    {
        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-readiness.index'))
            ->assertOk()
            ->assertSee('public/assets/brand')
            ->assertSee('public/assets/placeholders/team.svg')
            ->assertSee('public/assets/placeholders/generic.svg')
            ->assertSee('Future Media Manager scope');
    }

    public function test_media_readiness_module_is_read_only_and_does_not_mutate_media_records(): void
    {
        $mediaFile = MediaFile::query()->create([
            'disk' => 'public',
            'path' => 'assets/images/news/example.jpg',
            'filename' => 'example.jpg',
            'original_name' => 'example.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size_bytes' => 1024,
            'visibility' => 'public',
            'status' => 'active',
        ]);

        $this->assertFalse(Route::has('admin.media-readiness.create'));
        $this->assertFalse(Route::has('admin.media-readiness.store'));
        $this->assertFalse(Route::has('admin.media-readiness.edit'));
        $this->assertFalse(Route::has('admin.media-readiness.update'));
        $this->assertFalse(Route::has('admin.media-readiness.destroy'));

        $before = $mediaFile->fresh()->toArray();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-readiness.index'))
            ->assertOk()
            ->assertSee('1');

        $this->assertSame($before, $mediaFile->fresh()->toArray());
    }
}
