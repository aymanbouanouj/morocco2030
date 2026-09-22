<?php

namespace App\Http\Controllers\Admin;

use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Support\AssetFallback;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MediaReadinessController extends AdminController
{
    public function __invoke(): View
    {
        $this->authorize('viewAny', MediaFile::class);

        return view('admin.media-readiness.index', [
            'mediaSummary' => $this->mediaSummary(),
            'assetFolders' => $this->assetFolders(),
            'placeholderStatuses' => $this->placeholderStatuses(),
            'futureCategories' => $this->futureCategories(),
            'securityChecklist' => $this->securityChecklist(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mediaSummary(): array
    {
        if (! Schema::hasTable('media_files')) {
            return [
                'available' => false,
                'total' => 0,
                'active' => 0,
                'public' => 0,
                'trashed' => 0,
                'relations' => 0,
            ];
        }

        return [
            'available' => true,
            'total' => MediaFile::withTrashed()->count(),
            'active' => MediaFile::query()->where('status', 'active')->count(),
            'public' => MediaFile::query()->where('visibility', 'public')->count(),
            'trashed' => MediaFile::onlyTrashed()->count(),
            'relations' => Schema::hasTable('media_relations') ? MediaRelation::query()->count() : 0,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function assetFolders(): array
    {
        $folders = [
            'assets/brand' => 'Brand marks and favicon source exports',
            'assets/icons' => 'Approved UI icon exports',
            'assets/placeholders' => 'Fallback artwork for missing media',
            'assets/images/teams' => 'Future team logos and imagery',
            'assets/images/players' => 'Future player photos',
            'assets/images/cities' => 'Future host city imagery',
            'assets/images/stadiums' => 'Future stadium imagery',
            'assets/images/news' => 'Future news cover imagery',
            'assets/images/partners' => 'Future partner logos',
            'assets/vendor/leaflet' => 'Future local Leaflet vendor files',
            'assets/vendor/chartjs' => 'Future local Chart.js vendor files',
        ];

        return collect($folders)
            ->map(function (string $description, string $path) {
                $absolutePath = public_path(str_replace('/', DIRECTORY_SEPARATOR, $path));
                $files = is_dir($absolutePath)
                    ? array_values(array_filter(scandir($absolutePath) ?: [], fn (string $item) => $item !== '.' && $item !== '..' && is_file($absolutePath.DIRECTORY_SEPARATOR.$item)))
                    : [];

                return [
                    'path' => $path,
                    'description' => $description,
                    'exists' => is_dir($absolutePath),
                    'file_count' => count($files),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function placeholderStatuses(): array
    {
        return collect(AssetFallback::knownTypes())
            ->map(function (string $type) {
                $path = AssetFallback::placeholderPath($type);

                return [
                    'type' => $type,
                    'path' => $path,
                    'exists' => is_file(public_path(str_replace('/', DIRECTORY_SEPARATOR, $path))),
                ];
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function futureCategories(): array
    {
        return [
            'Team logos and federation marks',
            'Player profile photos',
            'Host city images',
            'Stadium and venue images',
            'News cover images',
            'Partner logos',
            'Brand/favicons/placeholders',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function securityChecklist(): array
    {
        return [
            'Uploads remain disabled until a dedicated workflow is approved.',
            'Allow only approved MIME types and extensions.',
            'Enforce maximum file size and image dimensions.',
            'Normalize filenames and avoid user-controlled public paths.',
            'Strip EXIF/location metadata where appropriate.',
            'Keep private files outside public disks.',
            'Audit upload, replace, detach, and delete operations.',
            'Require copyright/source review and meaningful alt text.',
            'Back up media files and verify restore procedures before production use.',
        ];
    }
}
