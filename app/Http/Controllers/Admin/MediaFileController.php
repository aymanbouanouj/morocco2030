<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MediaFiles\AttachMediaFileRequest;
use App\Http\Requests\Admin\MediaFiles\StoreMediaFileRequest;
use App\Http\Requests\Admin\MediaFiles\UpdateMediaFileMetadataRequest;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MediaFileController extends AdminController
{
    private const PREVIEW_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const ATTACHABLE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function __construct()
    {
        $this->authorizeResource(MediaFile::class, 'mediaFile');
    }

    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();

        $mediaFiles = MediaFile::query()
            ->with('uploadedBy')
            ->withCount('mediaRelations')
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('filename', 'like', "%{$search}%")
                        ->orWhere('original_name', 'like', "%{$search}%")
                        ->orWhere('path', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('alt_text', 'like', "%{$search}%");
                });
            })
            ->when($type === 'partner-logo', fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($type !== '' && $type !== 'partner-logo', fn (Builder $query) => $query->where('meta->category', $type))
            ->when($request->string('disk')->toString(), fn (Builder $query, string $disk) => $query->where('disk', $disk))
            ->when($request->string('mime_type')->toString(), fn (Builder $query, string $mimeType) => $query->where('mime_type', $mimeType))
            ->when($request->string('visibility')->toString(), fn (Builder $query, string $visibility) => $query->where('visibility', $visibility))
            ->when($request->string('status')->toString(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($request->string('attached')->toString() === 'yes', fn (Builder $query) => $query->has('mediaRelations'))
            ->when($request->string('attached')->toString() === 'no', fn (Builder $query) => $query->doesntHave('mediaRelations'))
            ->when($this->dateFilter($request, 'date_from'), fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($this->dateFilter($request, 'date_to'), fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $partnerLogoAssets = $this->partnerLogoAssets($request);
        $partnerLogoCount = $this->partnerLogoBaseQuery()->count();

        return view('admin.media-files.index', [
            'mediaFiles' => $mediaFiles,
            'partnerLogoAssets' => $partnerLogoAssets,
            'partnerLogoCount' => $partnerLogoCount,
            'totalVisualAssets' => MediaFile::query()->count() + $partnerLogoCount,
            'unattachedAssets' => MediaFile::query()->doesntHave('mediaRelations')->count(),
            'archivedAssets' => MediaFile::query()->where('status', 'archived')->count(),
            'filters' => $request->only([
                'search',
                'type',
                'disk',
                'mime_type',
                'visibility',
                'status',
                'attached',
                'date_from',
                'date_to',
            ]),
            'disks' => MediaFile::query()->select('disk')->distinct()->orderBy('disk')->pluck('disk'),
            'mimeTypes' => MediaFile::query()->select('mime_type')->distinct()->orderBy('mime_type')->pluck('mime_type'),
            'typeOptions' => $this->typeOptions(),
            'visibilities' => MediaFile::query()->select('visibility')->distinct()->orderBy('visibility')->pluck('visibility'),
            'statuses' => $this->statusOptions(),
            'previewUrls' => $this->previewUrls($mediaFiles->getCollection()->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.media-files.create', [
            'categories' => StoreMediaFileRequest::allowedCategories(),
            'maxUploadMegabytes' => StoreMediaFileRequest::MAX_UPLOAD_KILOBYTES / 1024,
        ]);
    }

    public function store(StoreMediaFileRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $validated = $request->validated();
        $category = (string) $validated['category'];
        $mimeType = (string) $file->getMimeType();
        $extension = $this->extensionForMimeType($mimeType);
        $filename = (string) Str::uuid().'.'.$extension;
        $directory = 'media/'.$category.'/'.now()->format('Y/m');
        $storedPath = $file->storeAs($directory, $filename, 'public');

        if (! is_string($storedPath) || $storedPath === '') {
            throw ValidationException::withMessages([
                'file' => 'The file could not be stored. Please try again.',
            ]);
        }

        [$width, $height] = $this->imageDimensions($file->getRealPath());

        $mediaFile = MediaFile::query()->create([
            'uploaded_by' => $request->user()?->id,
            'disk' => 'public',
            'path' => $storedPath,
            'filename' => $filename,
            'original_name' => $this->safeOriginalName($file->getClientOriginalName()),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size_bytes' => (int) ($file->getSize() ?: 0),
            'width' => $width,
            'height' => $height,
            'checksum' => $this->checksum($file->getRealPath()),
            'title' => $validated['title'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'visibility' => 'public',
            'status' => 'active',
            'meta' => [
                'category' => $category,
                'source' => 'admin_upload_mvp',
                'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
            ],
        ]);

        $this->recordAudit($request, $mediaFile, 'media_uploaded', null, $this->auditPayload($mediaFile, $category));

        return redirect()->route('admin.media-files.show', $mediaFile)
            ->with('success', 'Image uploaded successfully.');
    }

    public function show(MediaFile $mediaFile): View
    {
        $mediaFile->load(['uploadedBy', 'mediaRelations.mediable']);

        return view('admin.media-files.show', [
            'mediaFile' => $mediaFile,
            'previewUrl' => $this->previewUrl($mediaFile),
            'publicUrl' => $this->publicUrl($mediaFile),
            'metaJson' => $this->formatMeta($mediaFile->meta),
            'attachmentTargets' => $this->attachmentTargets(),
            'attachmentRoles' => AttachMediaFileRequest::allowedRoles(),
            'defaultAttachmentRole' => $this->defaultAttachmentRole($mediaFile),
            'canAttachMedia' => $this->canBeAttached($mediaFile),
        ]);
    }

    public function edit(MediaFile $mediaFile): View
    {
        return view('admin.media-files.edit', [
            'mediaFile' => $mediaFile,
            'previewUrl' => $this->previewUrl($mediaFile),
            'publicUrl' => $this->publicUrl($mediaFile),
            'visibilities' => UpdateMediaFileMetadataRequest::allowedVisibilities(),
        ]);
    }

    public function update(UpdateMediaFileMetadataRequest $request, MediaFile $mediaFile): RedirectResponse
    {
        $validated = $request->validated();
        $payload = [];

        foreach (['alt_text', 'caption', 'visibility'] as $field) {
            if ($request->has($field)) {
                $payload[$field] = $validated[$field] ?? null;
            }
        }

        $mediaFile->fill($payload);
        $changedFields = array_keys($mediaFile->getDirty());

        if ($changedFields === []) {
            return redirect()->route('admin.media-files.show', $mediaFile)
                ->with('success', 'No media metadata changes were detected.');
        }

        $oldValues = $this->metadataAuditPayload($mediaFile, $changedFields, true);
        $mediaFile->save();
        $newValues = $this->metadataAuditPayload($mediaFile->fresh(), $changedFields, false);

        $this->recordAudit($request, $mediaFile, 'media_updated', $oldValues, $newValues);

        return redirect()->route('admin.media-files.show', $mediaFile)
            ->with('success', 'Media metadata updated successfully. File, storage path, status, and relations were not changed.');
    }

    public function attach(AttachMediaFileRequest $request, MediaFile $mediaFile): RedirectResponse
    {
        $this->authorize('attach', $mediaFile);

        if (! $this->canBeAttached($mediaFile)) {
            throw ValidationException::withMessages([
                'media_file' => 'Only active public JPEG, PNG, or WebP media files can be attached.',
            ]);
        }

        $target = $request->targetModel();

        $this->authorize('update', $target);

        $validated = $request->validated();
        $role = (string) $validated['role'];
        $targetType = (string) $validated['target_type'];
        $previousPrimaryIds = MediaRelation::query()
            ->where('mediable_type', $target::class)
            ->where('mediable_id', $target->getKey())
            ->where('role', $role)
            ->where('is_primary', true)
            ->pluck('media_file_id')
            ->all();

        $relation = DB::transaction(function () use ($mediaFile, $target, $role): MediaRelation {
            MediaRelation::query()
                ->where('mediable_type', $target::class)
                ->where('mediable_id', $target->getKey())
                ->where('role', $role)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);

            return MediaRelation::query()->updateOrCreate(
                [
                    'media_file_id' => $mediaFile->id,
                    'mediable_type' => $target::class,
                    'mediable_id' => $target->getKey(),
                    'role' => $role,
                ],
                [
                    'sort_order' => 0,
                    'is_primary' => true,
                ],
            );
        });

        $this->recordAudit($request, $mediaFile, 'media_attached', null, [
            'media_file_id' => $mediaFile->id,
            'media_relation_id' => $relation->id,
            'target_type' => $targetType,
            'target_model' => $target::class,
            'target_id' => $target->getKey(),
            'role' => $role,
            'is_primary' => true,
            'previous_primary_media_file_ids' => $previousPrimaryIds,
        ]);

        return redirect()->route('admin.media-files.show', $mediaFile)
            ->with('success', 'Media file attached successfully.');
    }

    public function archive(Request $request, MediaFile $mediaFile): RedirectResponse
    {
        $this->authorize('archive', $mediaFile);

        if ($mediaFile->status === 'archived') {
            return redirect()->route('admin.media-files.show', $mediaFile)
                ->with('success', 'Media file is already archived.');
        }

        $previousStatus = $mediaFile->status;

        $mediaFile->update(['status' => 'archived']);

        $this->recordAudit(
            $request,
            $mediaFile,
            'media_archived',
            $this->statusAuditPayload($mediaFile, 'previous_status', $previousStatus),
            $this->statusAuditPayload($mediaFile->fresh(), 'new_status', 'archived'),
        );

        return redirect()->route('admin.media-files.show', $mediaFile)
            ->with('success', 'Media file archived. The database record, relations, and physical file were not deleted.');
    }

    public function restore(Request $request, MediaFile $mediaFile): RedirectResponse
    {
        $this->authorize('restore', $mediaFile);

        if ($mediaFile->status === 'active') {
            return redirect()->route('admin.media-files.show', $mediaFile)
                ->with('success', 'Media file is already active.');
        }

        $previousStatus = $mediaFile->status;

        $mediaFile->update(['status' => 'active']);

        $this->recordAudit(
            $request,
            $mediaFile,
            'media_restored',
            $this->statusAuditPayload($mediaFile, 'previous_status', $previousStatus),
            $this->statusAuditPayload($mediaFile->fresh(), 'new_status', 'active'),
        );

        return redirect()->route('admin.media-files.show', $mediaFile)
            ->with('success', 'Media file restored to active status.');
    }

    private function extensionForMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function imageDimensions(?string $path): array
    {
        if (! $path || ! is_file($path)) {
            return [null, null];
        }

        $dimensions = @getimagesize($path);

        if (! is_array($dimensions)) {
            return [null, null];
        }

        return [
            isset($dimensions[0]) ? (int) $dimensions[0] : null,
            isset($dimensions[1]) ? (int) $dimensions[1] : null,
        ];
    }

    private function checksum(?string $path): ?string
    {
        if (! $path || ! is_file($path)) {
            return null;
        }

        return hash_file('sha256', $path) ?: null;
    }

    private function safeOriginalName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = preg_replace('/[\x00-\x1F\x7F]+/', '', $name) ?: 'uploaded-image';

        return Str::limit($name, 255, '');
    }

    /**
     * @return array<string, mixed>
     */
    private function auditPayload(MediaFile $mediaFile, string $category): array
    {
        return [
            'media_file_id' => $mediaFile->id,
            'disk' => $mediaFile->disk,
            'path' => $mediaFile->path,
            'mime_type' => $mediaFile->mime_type,
            'size_bytes' => $mediaFile->size_bytes,
            'category' => $category,
            'visibility' => $mediaFile->visibility,
            'status' => $mediaFile->status,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function statusAuditPayload(?MediaFile $mediaFile, string $statusKey, string $status): array
    {
        return [
            'media_file_id' => $mediaFile?->id,
            $statusKey => $status,
            'disk' => $mediaFile?->disk,
            'relative_path' => $mediaFile?->path,
        ];
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    private function metadataAuditPayload(?MediaFile $mediaFile, array $fields, bool $useOriginal): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field] = $useOriginal ? $mediaFile?->getOriginal($field) : $mediaFile?->getAttribute($field);
        }

        return [
            'media_file_id' => $mediaFile?->id,
            'changed_fields' => $fields,
            'values' => $values,
        ];
    }

    /**
     * @param  array<int, MediaFile>  $mediaFiles
     * @return array<int, string|null>
     */
    private function previewUrls(array $mediaFiles): array
    {
        $urls = [];

        foreach ($mediaFiles as $mediaFile) {
            $urls[$mediaFile->id] = $this->previewUrl($mediaFile);
        }

        return $urls;
    }

    private function previewUrl(MediaFile $mediaFile): ?string
    {
        if (! in_array($mediaFile->mime_type, self::PREVIEW_MIME_TYPES, true)) {
            return null;
        }

        return $this->publicUrl($mediaFile);
    }

    private function publicUrl(MediaFile $mediaFile): ?string
    {
        if ($mediaFile->disk !== 'public' || $mediaFile->visibility !== 'public' || $mediaFile->status !== 'active') {
            return null;
        }

        try {
            return Storage::disk($mediaFile->disk)->url($mediaFile->path);
        } catch (\Throwable) {
            return null;
        }
    }

    private function partnerLogoBaseQuery(): Builder
    {
        return Partner::query()
            ->whereNotNull('logo_path')
            ->where('logo_path', '!=', '');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function partnerLogoAssets(Request $request): array
    {
        $type = $request->string('type')->toString();

        if ($type !== '' && $type !== 'partner-logo') {
            return [];
        }

        if ($request->string('mime_type')->toString() !== '') {
            return [];
        }

        if ($request->string('visibility')->toString() !== '') {
            return [];
        }

        if ($request->string('attached')->toString() === 'no') {
            return [];
        }

        $query = $this->partnerLogoBaseQuery()
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('logo_path', 'like', "%{$search}%")
                        ->orWhere('logo_alt', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($this->dateFilter($request, 'date_from'), fn (Builder $query, string $date) => $query->whereDate('updated_at', '>=', $date))
            ->when($this->dateFilter($request, 'date_to'), fn (Builder $query, string $date) => $query->whereDate('updated_at', '<=', $date))
            ->latest('updated_at')
            ->limit(50);

        return $query->get(['id', 'name', 'slug', 'status', 'logo_path', 'logo_alt', 'updated_at'])
            ->map(function (Partner $partner): array {
                return [
                    'id' => 'partner-logo-'.$partner->id,
                    'preview_url' => $partner->logoUrl(),
                    'source' => 'Partners',
                    'source_tag' => 'Partner Logo',
                    'owner' => 'Contextual asset',
                    'filename' => basename((string) $partner->logo_path),
                    'type' => 'Partner Logo',
                    'linked_to' => $partner->name,
                    'alt_text' => $partner->logoAlt($partner->name),
                    'status' => $partner->status,
                    'visibility' => 'local upload',
                    'uploaded_at' => $partner->updated_at,
                    'path' => $partner->logo_path,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function typeOptions(): array
    {
        $mediaCategories = MediaFile::query()
            ->select('meta->category as category')
            ->whereNotNull('meta->category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter(fn ($category): bool => is_string($category) && $category !== '')
            ->mapWithKeys(fn (string $category): array => [$category => Str::headline($category)])
            ->all();

        return ['partner-logo' => 'Partner Logo'] + $mediaCategories;
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function statusOptions(): \Illuminate\Support\Collection
    {
        return MediaFile::query()->select('status')->distinct()->pluck('status')
            ->merge($this->partnerLogoBaseQuery()->select('status')->distinct()->pluck('status'))
            ->filter(fn ($status): bool => is_string($status) && $status !== '')
            ->unique()
            ->sort()
            ->values();
    }

    private function canBeAttached(MediaFile $mediaFile): bool
    {
        return $mediaFile->visibility === 'public'
            && $mediaFile->status === 'active'
            && in_array($mediaFile->mime_type, self::ATTACHABLE_MIME_TYPES, true);
    }

    /**
     * @return array<string, array{label: string, role: string, records: array<int, array{id: int, label: string}>}>
     */
    private function attachmentTargets(): array
    {
        $targets = [];

        foreach (AttachMediaFileRequest::targetMap() as $type => $config) {
            $modelClass = $config['model'];
            $orderColumn = $config['order'];
            $records = $modelClass::query()
                ->orderBy($orderColumn)
                ->limit(150)
                ->get()
                ->map(fn (Model $model): array => [
                    'id' => (int) $model->getKey(),
                    'label' => $this->targetLabel($model),
                ])
                ->all();

            $targets[$type] = [
                'label' => $config['label'],
                'role' => $config['role'],
                'records' => $records,
            ];
        }

        return $targets;
    }

    private function targetLabel(Model $model): string
    {
        foreach (['name', 'title', 'display_name', 'short_name', 'slug'] as $attribute) {
            $value = $model->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value.' (#'.$model->getKey().')';
            }
        }

        return class_basename($model).' #'.$model->getKey();
    }

    private function defaultAttachmentRole(MediaFile $mediaFile): string
    {
        $category = is_array($mediaFile->meta) ? ($mediaFile->meta['category'] ?? null) : null;

        return match ($category) {
            'teams', 'partners' => 'logo',
            'players' => 'photo',
            'news' => 'cover',
            default => 'image',
        };
    }

    private function dateFilter(Request $request, string $key): ?string
    {
        $value = $request->string($key)->toString();

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    private function formatMeta(?array $meta): string
    {
        if ($meta === null || $meta === []) {
            return 'No metadata recorded.';
        }

        return json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ?: 'Unable to format metadata.';
    }
}
