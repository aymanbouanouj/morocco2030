<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\MediaFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MediaFileUploadMvpTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_media_upload_page_is_protected_and_permission_controlled(): void
    {
        $this->get(route('admin.media-files.create'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.media-files.create'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.media-files.create'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.create'))
            ->assertOk()
            ->assertSee('Safe image upload MVP')
            ->assertSee('JPG, PNG, WebP')
            ->assertSee('SVG, PDF, documents, audio, and video are blocked');
    }

    public function test_authorized_staff_can_upload_jpeg_image(): void
    {
        Storage::fake('public');

        $user = $this->makeStaffUser(['media.manage']);

        $response = $this->actingAs($user)->post(route('admin.media-files.store'), [
            'file' => UploadedFile::fake()->image('stadium.jpg', 1200, 800)->size(512),
            'category' => 'stadiums',
            'title' => 'Stadium upload',
            'alt_text' => 'Exterior stadium view',
            'caption' => 'Approved upload test image.',
        ]);

        $mediaFile = MediaFile::query()->firstOrFail();

        $response->assertRedirect(route('admin.media-files.show', $mediaFile));

        $this->assertSame($user->id, $mediaFile->uploaded_by);
        $this->assertSame('public', $mediaFile->disk);
        $this->assertStringStartsWith('media/stadiums/'.now()->format('Y/m').'/', $mediaFile->path);
        $this->assertSame('jpg', $mediaFile->extension);
        $this->assertSame('image/jpeg', $mediaFile->mime_type);
        $this->assertSame(1200, $mediaFile->width);
        $this->assertSame(800, $mediaFile->height);
        $this->assertSame('Stadium upload', $mediaFile->title);
        $this->assertSame('Exterior stadium view', $mediaFile->alt_text);
        $this->assertSame('Approved upload test image.', $mediaFile->caption);
        $this->assertSame('stadiums', $mediaFile->meta['category']);

        Storage::disk('public')->assertExists($mediaFile->path);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => MediaFile::class,
            'auditable_id' => $mediaFile->id,
            'action' => 'media_uploaded',
        ]);

        $auditLog = AuditLog::query()->where('action', 'media_uploaded')->firstOrFail();
        $this->assertSame($mediaFile->id, $auditLog->new_values['media_file_id']);
        $this->assertSame('stadiums', $auditLog->new_values['category']);
        $this->assertArrayNotHasKey('file', $auditLog->new_values);
    }

    public function test_authorized_staff_can_upload_png_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->post(route('admin.media-files.store'), [
                'file' => UploadedFile::fake()->image('partner.png', 640, 360)->size(256),
                'category' => 'partners',
                'alt_text' => 'Partner logo placeholder',
            ])
            ->assertRedirect();

        $mediaFile = MediaFile::query()->firstOrFail();

        $this->assertSame('png', $mediaFile->extension);
        $this->assertSame('image/png', $mediaFile->mime_type);
        $this->assertStringStartsWith('media/partners/', $mediaFile->path);
        Storage::disk('public')->assertExists($mediaFile->path);
    }

    public function test_svg_pdf_and_oversized_uploads_are_rejected_without_creating_records_or_files(): void
    {
        Storage::fake('public');

        $user = $this->makeStaffUser(['media.manage']);

        $invalidUploads = [
            UploadedFile::fake()->create('unsafe.svg', 10, 'image/svg+xml'),
            UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
            UploadedFile::fake()->image('oversized.jpg', 800, 600)->size(4097),
        ];

        foreach ($invalidUploads as $upload) {
            $this->actingAs($user)
                ->from(route('admin.media-files.create'))
                ->post(route('admin.media-files.store'), [
                    'file' => $upload,
                    'category' => 'news',
                    'alt_text' => 'Invalid upload',
                ])
                ->assertRedirect(route('admin.media-files.create'))
                ->assertSessionHasErrors('file');
        }

        $this->assertSame(0, MediaFile::query()->count());
        $this->assertSame(0, AuditLog::query()->where('action', 'media_uploaded')->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_failed_validation_with_invalid_category_does_not_store_file(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->from(route('admin.media-files.create'))
            ->post(route('admin.media-files.store'), [
                'file' => UploadedFile::fake()->image('valid.jpg', 800, 600)->size(128),
                'category' => 'unknown-category',
            ])
            ->assertRedirect(route('admin.media-files.create'))
            ->assertSessionHasErrors('category');

        $this->assertSame(0, MediaFile::query()->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_upload_detail_does_not_expose_raw_local_filesystem_path(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->post(route('admin.media-files.store'), [
                'file' => UploadedFile::fake()->image('city.jpg', 800, 600)->size(128),
                'category' => 'cities',
            ])
            ->assertRedirect();

        $mediaFile = MediaFile::query()->firstOrFail();

        $this->actingAs($this->makeStaffUser(['media.manage']))
            ->get(route('admin.media-files.show', $mediaFile))
            ->assertOk()
            ->assertSee($mediaFile->path)
            ->assertDontSee(storage_path(), false)
            ->assertDontSee('C:\\xampp', false);
    }

    public function test_no_delete_replace_routes_exist_for_media_upload_mvp(): void
    {
        $this->assertTrue(Route::has('admin.media-files.create'));
        $this->assertTrue(Route::has('admin.media-files.store'));
        $this->assertTrue(Route::has('admin.media-relations.destroy'));
        $this->assertTrue(Route::has('admin.media-files.archive'));
        $this->assertTrue(Route::has('admin.media-files.restore'));
        $this->assertTrue(Route::has('admin.media-files.edit'));
        $this->assertTrue(Route::has('admin.media-files.update'));
        $this->assertFalse(Route::has('admin.media-files.destroy'));
        $this->assertFalse(Route::has('admin.media-files.replace'));
    }
}
