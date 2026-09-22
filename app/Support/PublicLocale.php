<?php

namespace App\Support;

use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PublicLocale
{
    public const SESSION_KEY = 'public_locale';

    protected ?Collection $languages = null;

    public function availableLanguages(): Collection
    {
        if ($this->languages !== null) {
            return $this->languages;
        }

        return $this->languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function currentLanguage(?string $requested = null): ?Language
    {
        $languages = $this->availableLanguages();

        if ($languages->isEmpty()) {
            return null;
        }

        return $this->findLanguage($languages, $requested)
            ?? $this->findLanguage($languages, session(self::SESSION_KEY))
            ?? $this->findLanguage($languages, config('app.locale'))
            ?? $languages->firstWhere('is_default', true)
            ?? $languages->first();
    }

    public function apply(?string $requested = null): ?Language
    {
        $language = $this->currentLanguage($requested);
        $locale = $language?->code ?? config('app.locale', 'en');
        $fallbackLocale = $this->fallbackLanguage($language)?->code ?? config('app.fallback_locale', 'en');

        app()->setLocale($locale);
        app('translator')->setFallback($fallbackLocale);
        Carbon::setLocale($locale);

        return $language;
    }

    public function fallbackLanguage(?Language $currentLanguage = null): ?Language
    {
        $languages = $this->availableLanguages();

        if ($languages->isEmpty()) {
            return null;
        }

        $configured = $this->findLanguage($languages, config('app.fallback_locale'))
            ?? $this->findLanguage($languages, config('app.locale'));

        if ($configured) {
            return $configured;
        }

        return $languages
            ->first(fn (Language $language) => ! $currentLanguage || ! $language->is($currentLanguage))
            ?? $currentLanguage
            ?? $languages->first();
    }

    protected function findLanguage(Collection $languages, ?string $identifier): ?Language
    {
        if (! filled($identifier)) {
            return null;
        }

        return $languages->first(function (Language $language) use ($identifier) {
            return in_array($identifier, [$language->code, $language->locale], true);
        });
    }
}
