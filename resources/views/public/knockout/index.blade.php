@extends('public.layouts.app')

@section('title', __('Knockout Stage').' | '.__('Morocco 2030'))
@section('meta_description', __('Browse the knockout rounds and follow the road to the final.'))

@push('head')
    <style>
        @media (min-width: 981px) {
            .page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals) {
                display: grid;
                grid-template-rows: auto minmax(0, 1fr);
            }

            .page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals)::before,
            .page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals)::after {
                display: none;
            }

            .page-inner .knockout-bracket--symmetric .knockout-round__matches--tree {
                display: grid;
                grid-template-rows: repeat(var(--round-match-count), minmax(0, 1fr));
                align-items: stretch;
                min-height: 0;
                gap: 0;
            }

            .page-inner .knockout-bracket--symmetric .knockout-match-slot {
                position: relative;
                display: flex;
                align-items: center;
                min-height: 0;
                padding-block: 0.08rem;
            }

            .page-inner .knockout-bracket--symmetric .knockout-match-slot > .knockout-match-card {
                z-index: 1;
                width: 100%;
            }

            .page-inner .knockout-bracket--symmetric .knockout-match-slot::after,
            .page-inner .knockout-bracket--symmetric .knockout-match-slot__inbound {
                position: absolute;
                top: 50%;
                z-index: 0;
                height: 1px;
                background: rgba(212, 173, 82, 0.62);
                content: '';
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--left .knockout-match-slot::after {
                left: 100%;
                width: 0.18rem;
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--right .knockout-match-slot::after {
                right: 100%;
                width: 0.18rem;
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--left .knockout-match-slot__inbound {
                right: 100%;
                width: 0.16rem;
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--right .knockout-match-slot__inbound {
                left: 100%;
                width: 0.16rem;
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--left .knockout-round:first-of-type .knockout-match-slot__inbound,
            .page-inner .knockout-bracket--symmetric .knockout-wing--right .knockout-round:last-of-type .knockout-match-slot__inbound {
                display: none;
            }

            .page-inner .knockout-bracket--symmetric .knockout-match-slot:nth-child(odd):not(:last-child)::before {
                position: absolute;
                top: 50%;
                z-index: 0;
                height: 100%;
                border-color: rgba(212, 173, 82, 0.62);
                content: '';
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--left .knockout-match-slot:nth-child(odd):not(:last-child)::before {
                left: calc(100% + 0.17rem);
                border-left: 1px solid;
            }

            .page-inner .knockout-bracket--symmetric .knockout-wing--right .knockout-match-slot:nth-child(odd):not(:last-child)::before {
                right: calc(100% + 0.17rem);
                border-right: 1px solid;
            }

            .page-inner .knockout-bracket--symmetric .knockout-final-stack {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $pathRoundOrder = ['round_of_32', 'round_of_16', 'quarter_final', 'semi_final'];
        $pathRounds = $rounds->except(['third_place', 'final']);
        $finalMatches = $rounds->get('final', collect());
        $thirdPlaceMatches = $rounds->get('third_place', collect());
        $totalKnockoutMatches = $rounds->flatten(1)->count();
        $splitRounds = collect($pathRoundOrder)
            ->filter(fn ($stageType) => $rounds->has($stageType))
            ->mapWithKeys(function ($stageType) use ($rounds) {
                $matches = $rounds->get($stageType, collect())->values();
                $half = (int) ceil($matches->count() / 2);

                return [$stageType => [
                    'left' => $matches->take($half)->values(),
                    'right' => $matches->slice($half)->values(),
                ]];
            });
        $leftRounds = $splitRounds->map(fn ($split) => $split['left'])->filter(fn ($matches) => $matches->isNotEmpty());
        $rightRounds = $splitRounds->map(fn ($split) => $split['right'])->filter(fn ($matches) => $matches->isNotEmpty())->reverse();
    @endphp

    <div class="entity-ref-page entity-ref-page--knockout">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Knockout') }}</span>
                <h1>{{ __('Knockout Bracket') }}</h1>
                <p>{{ __('Track the road to the final.') }}</p>

                @if ($rounds->isNotEmpty())
                    @php($completedMatches = $rounds->flatten(1)->where('status', 'completed')->count())
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($pathRounds->count() + ($finalMatches->isNotEmpty() ? 1 : 0)) }}</strong>
                            <span>{{ __('Rounds') }}</span>
                        </div>
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($totalKnockoutMatches) }}</strong>
                            <span>{{ __('Matches') }}</span>
                        </div>
                        @if ($completedMatches > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($completedMatches) }}</strong>
                                <span>{{ __('Completed') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

    <section class="page-section knockout-page-section entity-ref-section">
        <div class="section-shell knockout-shell knockout-ref-bracket knockout-shell--polished knockout-shell--strict knockout-shell--world-class knockout-shell--readable">
            @if ($rounds->isNotEmpty())
                <div class="knockout-command">
                    <div>
                        <span class="section-header__eyebrow">{{ __('Knockout Map') }}</span>
                        <h1>{{ __('Road To The Final') }}</h1>
                    </div>

                    <div class="knockout-command__stats" aria-label="{{ __('Bracket summary') }}">
                        <span>
                            <strong>{{ $pathRounds->count() + ($finalMatches->isNotEmpty() ? 1 : 0) }}</strong>
                            {{ __('Rounds') }}
                        </span>
                        <span>
                            <strong>{{ $totalKnockoutMatches }}</strong>
                            {{ __('Fixtures') }}
                        </span>
                    </div>
                </div>

                <div class="knockout-scroll knockout-fit-viewport" data-knockout-fit aria-label="{{ __('Knockout bracket') }}">
                    <div class="knockout-fit-canvas">
                        <div class="knockout-bracket knockout-bracket--symmetric knockout-bracket--fit knockout-bracket--world-class knockout-bracket--readable">
                            <div class="knockout-wing knockout-wing--left">
                                <div class="knockout-wing__title">
                                    <span>{{ __('Left bracket path') }}</span>
                                    <strong>{{ __('Route to the final') }}</strong>
                                </div>

                                @foreach ($leftRounds as $stageType => $matches)
                                    <section class="knockout-round knockout-round--{{ str_replace('_', '-', $stageType) }} knockout-round--left">
                                        <div class="knockout-round__header">
                                            <span>{{ __('Left Path') }}</span>
                                            <h3>{{ \App\Support\TournamentFormatting::stageLabel($stageType) }}</h3>
                                            <small>{{ trans_choice('{1}:count fixture|[2,*]:count fixtures', $matches->count(), ['count' => $matches->count()]) }}</small>
                                        </div>

                                        <div class="knockout-round__matches knockout-round__matches--tree" style="--round-match-count: {{ $matches->count() }}">
                                            @foreach ($matches as $match)
                                                <div class="knockout-match-slot">
                                                    <span class="knockout-match-slot__inbound" aria-hidden="true"></span>
                                                    @include('public.partials.bracket-match-card', [
                                                        'match' => $match,
                                                        'variant' => 'standard',
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>

                            <section class="knockout-final-axis" aria-label="{{ __('Final path') }}">
                                <div class="knockout-final-axis__cap">
                                    <span>{{ __('Destination') }}</span>
                                    <strong>{{ config('app.name', 'MOROCCO 2030') }}</strong>
                                </div>

                                <div class="knockout-final-axis__line" aria-hidden="true"></div>

                                <div class="knockout-round knockout-round--finals">
                                    <div class="knockout-round__header knockout-round__header--final">
                                        <span>{{ __('Final') }}</span>
                                        <h3>{{ __('Final Weekend') }}</h3>
                                        <small>{{ __('Title and podium fixtures') }}</small>
                                    </div>

                                    <div class="knockout-final-stack">
                                        @forelse ($finalMatches as $match)
                                            @include('public.partials.bracket-match-card', [
                                                'match' => $match,
                                                'variant' => 'final',
                                            ])
                                        @empty
                                            @include('public.partials.empty-state', [
                                                'title' => __('Final fixture not available.'),
                                                'message' => __('The final will appear once the knockout structure is confirmed.'),
                                            ])
                                        @endforelse

                                        @foreach ($thirdPlaceMatches as $match)
                                            @include('public.partials.bracket-match-card', [
                                                'match' => $match,
                                                'variant' => 'third',
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </section>

                            <div class="knockout-wing knockout-wing--right">
                                <div class="knockout-wing__title">
                                    <span>{{ __('Right bracket path') }}</span>
                                    <strong>{{ __('Route to the final') }}</strong>
                                </div>

                                @foreach ($rightRounds as $stageType => $matches)
                                    <section class="knockout-round knockout-round--{{ str_replace('_', '-', $stageType) }} knockout-round--right">
                                        <div class="knockout-round__header">
                                            <span>{{ __('Right Path') }}</span>
                                            <h3>{{ \App\Support\TournamentFormatting::stageLabel($stageType) }}</h3>
                                            <small>{{ trans_choice('{1}:count fixture|[2,*]:count fixtures', $matches->count(), ['count' => $matches->count()]) }}</small>
                                        </div>

                                        <div class="knockout-round__matches knockout-round__matches--tree" style="--round-match-count: {{ $matches->count() }}">
                                            @foreach ($matches as $match)
                                                <div class="knockout-match-slot">
                                                    <span class="knockout-match-slot__inbound" aria-hidden="true"></span>
                                                    @include('public.partials.bracket-match-card', [
                                                        'match' => $match,
                                                        'variant' => 'standard',
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="knockout-legend">
                    <span><i class="knockout-legend__dot knockout-legend__dot--resolved"></i>{{ __('Resolved team') }}</span>
                    <span><i class="knockout-legend__dot knockout-legend__dot--pending"></i>{{ __('Qualification source pending') }}</span>
                    <span><i class="knockout-legend__dot knockout-legend__dot--final"></i>{{ __('Final path') }}</span>
                </div>
            @else
                <div class="entity-ref-empty">
                @include('public.partials.empty-state', [
                    'title' => __('No knockout fixtures are available.'),
                    'message' => __('The knockout bracket will appear here as teams qualify.'),
                ])
                </div>
            @endif
        </div>
    </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const viewports = document.querySelectorAll('[data-knockout-fit]');

            if (!viewports.length) {
                return;
            }

            const desktopQuery = window.matchMedia('(min-width: 981px)');

            const resetCanvas = (canvas, board) => {
                canvas.style.removeProperty('width');
                canvas.style.removeProperty('height');
                canvas.style.removeProperty('--knockout-auto-scale');
                canvas.classList.remove('is-auto-scaled');
                board.style.removeProperty('transform');
            };

            const fitViewport = (viewport) => {
                const canvas = viewport.querySelector('.knockout-fit-canvas');
                const board = canvas?.querySelector('.knockout-bracket');

                if (!canvas || !board) {
                    return;
                }

                resetCanvas(canvas, board);

                if (!desktopQuery.matches) {
                    return;
                }

                const availableWidth = viewport.clientWidth;
                const naturalWidth = board.scrollWidth;
                const naturalHeight = board.scrollHeight;

                if (!availableWidth || !naturalWidth || !naturalHeight) {
                    return;
                }

                const widthScale = availableWidth / naturalWidth;
                const scale = widthScale < 1
                    ? Math.max(0.92, Math.min(1, widthScale))
                    : 1;

                canvas.style.setProperty('--knockout-auto-scale', scale.toFixed(3));
                canvas.style.width = `${Math.ceil(naturalWidth * scale)}px`;
                canvas.style.height = `${Math.ceil(naturalHeight * scale)}px`;
                canvas.classList.toggle('is-auto-scaled', scale < 0.995);
                board.style.transform = `scale(${scale})`;
            };

            const fitAll = () => {
                viewports.forEach(fitViewport);
            };

            const scheduleFit = () => window.requestAnimationFrame(fitAll);

            if ('ResizeObserver' in window) {
                const observer = new ResizeObserver(scheduleFit);
                viewports.forEach((viewport) => {
                    observer.observe(viewport);
                    const board = viewport.querySelector('.knockout-bracket');

                    if (board) {
                        observer.observe(board);
                    }
                });
            }

            window.addEventListener('load', scheduleFit);
            window.addEventListener('resize', scheduleFit);
            scheduleFit();
        })();
    </script>
@endpush
