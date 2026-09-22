<section class="page-header" aria-labelledby="page-header-title">
    <div class="page-header__content">
        @isset($eyebrow)
            <span class="page-header__eyebrow">{{ $eyebrow }}</span>
        @endisset

        <h1 id="page-header-title">{{ $title }}</h1>

        @isset($summary)
            <p>{{ $summary }}</p>
        @endisset

        @isset($actions)
            <div class="page-header__actions" role="group" aria-label="{{ __('Page actions') }}">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
