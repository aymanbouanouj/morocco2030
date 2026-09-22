<?php

namespace App\Support;

class AssetFallback
{
    /**
     * @var array<string, string>
     */
    private const PLACEHOLDERS = [
        'team' => 'assets/placeholders/team.svg',
        'player' => 'assets/placeholders/player.svg',
        'city' => 'assets/placeholders/city.svg',
        'stadium' => 'assets/placeholders/stadium.svg',
        'news' => 'assets/placeholders/news.svg',
        'partner' => 'assets/placeholders/partner.svg',
        'generic' => 'assets/placeholders/generic.svg',
    ];

    public static function placeholderPath(string $type = 'generic'): string
    {
        return self::PLACEHOLDERS[$type] ?? self::PLACEHOLDERS['generic'];
    }

    public static function placeholderUrl(string $type = 'generic'): string
    {
        return asset(self::placeholderPath($type));
    }

    /**
     * @return array<int, string>
     */
    public static function knownTypes(): array
    {
        return array_keys(self::PLACEHOLDERS);
    }
}
