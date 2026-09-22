@php
    $newsTitle = \App\Support\PublicContent::field($news, 'title') ?? $news->title;
    $newsSummary = \App\Support\PublicContent::field($news, 'summary');

    if (! is_string($newsSummary) || trim($newsSummary) === '') {
        $newsBody = \App\Support\PublicContent::field($news, 'body') ?? $news->body;
        $newsSummary = \Illuminate\Support\Str::limit(strip_tags(is_string($newsBody) ? $newsBody : ''), 140);
    }
@endphp
@php($media = \App\Support\PublicMedia::primaryData($news, null, $newsTitle))
@php($categoryName = \App\Support\PublicContent::field($news->category, 'name') ?? $news->category?->name)
@php($useRefStyle = $refStyle ?? false)

<article
    @class([
        'news-card',
        'entity-ref-card' => $useRefStyle,
        'entity-ref-card--news' => $useRefStyle,
        'entity-ref-card--featured' => $useRefStyle && ($featured ?? false),
    ])
    aria-labelledby="news-card-title-{{ $news->id ?? $news->slug }}"
>
    <a href="{{ route('news.show', $news->slug) }}" @class(['news-card__media-link', 'entity-ref-card__media' => $useRefStyle, 'news-ref-cover' => $useRefStyle]) tabindex="-1" aria-hidden="true">
        <div @class(['news-card__media', 'entity-card__media--bound', 'entity-ref-card__media-inner' => $useRefStyle])>
            @include('public.partials.local-entity-media', [
                'type' => 'news',
                'slug' => $news->slug,
                'name' => $newsTitle,
                'media' => $media,
                'context' => 'card',
            ])
        </div>
    </a>

    <div @class(['news-card__body', 'entity-ref-card__body' => $useRefStyle])>
        <div class="badge-row">
            @if ($categoryName)
                <span class="meta-pill">{{ $categoryName }}</span>
            @endif

            @if ($news->published_at)
                <time class="meta-pill" datetime="{{ $news->published_at->toIso8601String() }}">{{ $news->published_at->translatedFormat('M j, Y') }}</time>
            @endif
        </div>

        <h3 @class(['news-card__title', 'entity-ref-card__title' => $useRefStyle]) id="news-card-title-{{ $news->id ?? $news->slug }}">
            <a href="{{ route('news.show', $news->slug) }}">{{ $newsTitle }}</a>
        </h3>

        <p @class(['news-card__summary', 'entity-ref-card__excerpt' => $useRefStyle])>
            {{ $newsSummary }}
        </p>

        @if ($useRefStyle)
            <a href="{{ route('news.show', $news->slug) }}" class="entity-ref-card__cta">{{ __('Read More') }}</a>
        @else
            <div class="news-card__footer">
                <span>{{ __('Read Story') }}</span>
            </div>
        @endif
    </div>
</article>
