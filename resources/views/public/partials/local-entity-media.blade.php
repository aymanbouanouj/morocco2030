@php
    $context = $context ?? 'card';
    $type = $type ?? 'generic';
    $lazy = $lazy ?? ($context !== 'hero');
    $flagSize = $flagSize ?? match ($context) {
        'hero' => 'large',
        'table' => 'small',
        default => 'medium',
    };
    $media = $media ?? (isset($model) ? \App\Support\PublicMedia::primaryData($model, $role ?? null, $name ?? null) : ['url' => null, 'alt' => $name ?? null]);
    $imageUrl = $media['url'] ?? null;
    $imageUrl = is_string($imageUrl) && $imageUrl !== '' ? $imageUrl : null;
    $entityName = $name ?? '';
    $imageAlt = $media['alt'] ?? $entityName;
    $model = $model ?? null;
    $modelMeta = $model && isset($model->meta) && is_array($model->meta) ? $model->meta : [];
    $isTbdPlaceholder = $type === 'team' && (bool) data_get($modelMeta, 'placeholder');
    $useBadge = false;
    $badgeText = $isTbdPlaceholder ? 'TBD' : ($entityName !== '' ? str($entityName)->substr(0, 2)->upper()->toString() : '?');
    $imageClass = trim('media-bound-image '.($imageClass ?? ''));
    $isFlagImage = false;

    if ($isTbdPlaceholder) {
        $imageUrl = null;
        $useBadge = true;
    }

    $fifaTeamCodeToFlagIso = [
        'mar' => 'ma',
        'bra' => 'br',
        'fra' => 'fr',
        'esp' => 'es',
        'arg' => 'ar',
        'por' => 'pt',
        'ger' => 'de',
        'deu' => 'de',
        'eng' => 'gb-eng',
        'ned' => 'nl',
        'nld' => 'nl',
        'bel' => 'be',
        'ita' => 'it',
        'cro' => 'hr',
        'hrv' => 'hr',
        'usa' => 'us',
        'mex' => 'mx',
        'can' => 'ca',
        'jpn' => 'jp',
        'kor' => 'kr',
        'sen' => 'sn',
        'egy' => 'eg',
        'tun' => 'tn',
        'alg' => 'dz',
        'dza' => 'dz',
        'nga' => 'ng',
        'gha' => 'gh',
        'cmr' => 'cm',
        'rsa' => 'za',
        'zaf' => 'za',
        'qat' => 'qa',
        'ksa' => 'sa',
        'sau' => 'sa',
        'aus' => 'au',
        'uru' => 'uy',
        'col' => 'co',
        'chi' => 'cl',
        'chl' => 'cl',
        'ecu' => 'ec',
        'per' => 'pe',
        'par' => 'py',
        'pry' => 'py',
        'sui' => 'ch',
        'che' => 'ch',
        'aut' => 'at',
        'den' => 'dk',
        'dnk' => 'dk',
        'swe' => 'se',
        'nor' => 'no',
        'pol' => 'pl',
        'tur' => 'tr',
        'ukr' => 'ua',
        'cze' => 'cz',
        'srp' => 'rs',
        'srb' => 'rs',
        'wal' => 'gb-wls',
        'wls' => 'gb-wls',
        'sco' => 'gb-sct',
        'irl' => 'ie',
        'irn' => 'ir',
        'crc' => 'cr',
        'pan' => 'pa',
        'jam' => 'jm',
        'mor' => 'ma',
    ];

    $resolveFlagIso = static function (?string $code) use ($fifaTeamCodeToFlagIso): ?string {
        if ($code === null || trim($code) === '') {
            return null;
        }

        $normalized = strtolower(trim($code));

        if (strlen($normalized) === 2 && ctype_alpha($normalized)) {
            return $normalized;
        }

        if (isset($fifaTeamCodeToFlagIso[$normalized])) {
            return $fifaTeamCodeToFlagIso[$normalized];
        }

        return null;
    };

    $resolveFlagAssetPath = static function (string $aspect, ?string $iso): ?string {
        if (! $iso) {
            return null;
        }

        $candidates = array_values(array_unique(array_filter([
            $iso,
            str_contains($iso, '-') ? explode('-', $iso, 2)[0] : null,
        ])));

        foreach ($candidates as $candidate) {
            $path = "assets/images/flag/flags/{$aspect}/{$candidate}.svg";

            if (file_exists(public_path($path))) {
                return $path;
            }
        }

        return null;
    };

    if (! $imageUrl && ! $useBadge) {
        $slug = $slug ?? null;
        $code = isset($code) ? (string) $code : null;

        if ($type === 'team') {
            $flagIso = $resolveFlagIso($code);
            $flagPath = $resolveFlagAssetPath('4x3', $flagIso);

            if ($flagPath) {
                $imageUrl = asset($flagPath);
                $isFlagImage = true;
                $imageClass .= ' team-flag flag-ref flag-ref--'.$flagSize;
                $imageAlt = trim($entityName) !== '' ? $entityName.' '.__('flag') : __('Team flag');
            }
        }

        if ($type === 'player' && $code) {
            $flagIso = $resolveFlagIso($code);
            $flagPath = $resolveFlagAssetPath('1x1', $flagIso);

            if ($flagPath) {
                $imageUrl = asset($flagPath);
                $isFlagImage = true;
                $imageClass .= ' player-avatar player-avatar--flag flag-ref flag-ref--'.$flagSize;
                $imageAlt = trim($entityName) !== '' ? $entityName.' '.__('flag') : __('Nationality flag');
            }
        }

        if ($type === 'partner' && $slug) {
            $localPath = "assets/images/partners/{$slug}.png";
            if (file_exists(public_path($localPath))) {
                $imageUrl = asset($localPath);
            }
        }

        if (! $imageUrl) {
            if ($context === 'partner' || $type === 'partner') {
                $useBadge = true;
            } else {
                $placeholderType = match ($type) {
                    'stadium' => 'stadium',
                    'city' => 'city',
                    'team' => 'team',
                    'player' => 'player',
                    'news' => 'news',
                    'partner' => 'partner',
                    default => 'generic',
                };
                $imageUrl = \App\Support\AssetFallback::placeholderUrl($placeholderType);
                $imageClass .= ' media-bound-image--placeholder';
            }
        }
    }

    if ($type === 'team' || ($type === 'player' && str_contains($imageClass, 'player-avatar'))) {
        $objectFit = 'contain';
    } elseif ($type === 'partner' || str_contains($imageClass, 'placeholder')) {
        $objectFit = 'contain';
    } else {
        $objectFit = 'cover';
    }

    $flagWrapperClass = collect([
        'flag-ref-wrap',
        'flag-ref-wrap--'.$flagSize,
        'team-ref-flag' => $type === 'team' && ($isFlagImage || str_contains($imageClass, 'team-flag')) && ! in_array($context, ['table', 'card'], true),
        'player-ref-team-flag' => $type === 'player' && str_contains($imageClass, 'player-avatar--flag'),
    ])->filter()->implode(' ');
@endphp

@if ($useBadge)
    <span class="placeholder-badge partner-logo partner-logo--text" aria-hidden="true">{{ $badgeText }}</span>
@elseif ($isFlagImage || ($type === 'team' && str_contains($imageClass, 'team-flag')))
    <span @class([$flagWrapperClass])>
        <img
            @class([$imageClass, 'entity-card__image' => $context === 'card', 'entity-detail-hero__media' => $context === 'hero', 'partner-logo' => $context === 'partner'])
            src="{{ $imageUrl }}"
            alt="{{ $imageAlt }}"
            @if ($lazy) loading="lazy" @endif
            decoding="async"
            onerror="this.closest('span')?.classList.add('is-media-error'); this.remove();"
            style="object-fit: {{ $objectFit }};"
        >
    </span>
@else
    <img
        @class([$imageClass, 'entity-card__image' => $context === 'card', 'entity-detail-hero__media' => $context === 'hero', 'partner-logo' => $context === 'partner'])
        src="{{ $imageUrl }}"
        alt="{{ $imageAlt }}"
        @if ($lazy) loading="lazy" @endif
        decoding="async"
        onerror="this.remove();"
        style="object-fit: {{ $objectFit }};"
    >
@endif
