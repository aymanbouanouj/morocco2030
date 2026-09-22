<?php

namespace App\Translation;

use App\Models\InterfaceTranslation;
use App\Models\Language;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DatabaseTranslationLoader implements Loader
{
    protected array $loaded = [];

    protected ?bool $tablesAvailable = null;

    public function __construct(
        protected Loader $loader,
    ) {
    }

    public function load($locale, $group, $namespace = null): array
    {
        $lines = $this->loader->load($locale, $group, $namespace);
        $databaseLines = $this->databaseLines((string) $locale, (string) $group, $namespace);

        if ($databaseLines === []) {
            return $lines;
        }

        return array_replace($lines, $databaseLines);
    }

    public function addNamespace($namespace, $hint): void
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path): void
    {
        $this->loader->addJsonPath($path);
    }

    public function namespaces(): array
    {
        return $this->loader->namespaces();
    }

    protected function databaseLines(string $locale, string $group, ?string $namespace): array
    {
        $cacheKey = implode('|', [$locale, $group, $namespace ?? '*']);

        if (array_key_exists($cacheKey, $this->loaded)) {
            return $this->loaded[$cacheKey];
        }

        if (! $this->tablesExist()) {
            return $this->loaded[$cacheKey] = [];
        }

        $language = Language::query()
            ->where('code', $locale)
            ->orWhere('locale', $locale)
            ->first();

        if (! $language) {
            return $this->loaded[$cacheKey] = [];
        }

        $isJsonLoad = $group === '*' && in_array($namespace, [null, '*'], true);

        return $this->loaded[$cacheKey] = InterfaceTranslation::query()
            ->where('language_id', $language->id)
            ->whereIn('namespace', $this->namespaceCandidates($namespace))
            ->where('group_name', $isJsonLoad ? 'json' : $group)
            ->orderBy('translation_key')
            ->pluck('value', 'translation_key')
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->all();
    }

    protected function namespaceCandidates(?string $namespace): array
    {
        if ($namespace === null || $namespace === '*') {
            return ['public', '*'];
        }

        return [$namespace, '*'];
    }

    protected function tablesExist(): bool
    {
        if ($this->tablesAvailable !== null) {
            return $this->tablesAvailable;
        }

        try {
            return $this->tablesAvailable = Schema::hasTable('languages')
                && Schema::hasTable('interface_translations');
        } catch (Throwable) {
            return $this->tablesAvailable = false;
        }
    }
}
