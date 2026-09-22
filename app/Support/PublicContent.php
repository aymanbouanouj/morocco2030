<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class PublicContent
{
    public static function field(?Model $model, string $field, ?string $locale = null): ?string
    {
        if (! $model) {
            return null;
        }

        $attribute = $model->getAttribute($field);
        $fallback = self::stringValue($attribute);

        if (! method_exists($model, 'translations')) {
            return $fallback;
        }

        $locale = $locale ?: app()->getLocale();

        $translations = $model->relationLoaded('translations')
            ? $model->getRelation('translations')->loadMissing('language')
            : $model->translations()->with('language')->get();

        $candidates = $translations
            ->filter(fn ($translation) => $translation->field === $field && filled($translation->value))
            ->values();

        if ($candidates->isEmpty()) {
            return $fallback;
        }

        $preferred = $candidates->first(function ($translation) use ($locale) {
            $language = $translation->language;

            return $language
                && in_array($locale, [$language->code, $language->locale], true);
        });

        $fallbackLocale = app('translator')->getFallback();

        $fallbackTranslation = $candidates->first(function ($translation) use ($fallbackLocale) {
            $language = $translation->language;

            return $language
                && in_array($fallbackLocale, [$language->code, $language->locale], true);
        });

        $default = $candidates->first(fn ($translation) => $translation->language?->is_default);

        return self::stringValue(
            $preferred?->value
            ?? $fallback
            ?? $fallbackTranslation?->value
            ?? $default?->value
            ?? $candidates->first()?->value
        );
    }

    protected static function stringValue(mixed $value): ?string
    {
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        return null;
    }
}
