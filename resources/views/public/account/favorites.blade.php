@extends('public.layouts.app')

@section('title', __('Favorites').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Favorites'),
        'summary' => __('Everything you have saved for quick access across the public portal.'),
    ])

    <section class="page-section account-layout">
        <aside class="account-sidebar">
            @include('public.account._nav')
        </aside>

        <div class="panel account-panel">
            <h2>{{ __('Saved items') }}</h2>
            <p>{{ __('Your saved teams, fixtures, players, destinations, and stories appear here.') }}</p>

            @if ($favorites->isEmpty())
                <div class="account-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No favorites yet'),
                        'message' => __('Explore the tournament and save the pages you want to revisit quickly.'),
                    ])
                </div>
            @else
                <div class="account-inline-list" style="margin-top: 1.25rem;">
                    @foreach ($favorites as $favorite)
                        <article class="list-card">
                            <small>{{ $favorite['type'] }}</small>
                            @if ($favorite['url'])
                                <a href="{{ $favorite['url'] }}"><strong>{{ $favorite['title'] }}</strong></a>
                            @else
                                <strong>{{ $favorite['title'] }}</strong>
                            @endif
                            <p>{{ $favorite['summary'] }}</p>
                            <small>{{ __('Saved on :date', ['date' => optional($favorite['created_at'])->translatedFormat('M j, Y')]) }}</small>
                        </article>
                    @endforeach
                </div>

                <div style="margin-top: 1.25rem;">
                    {{ $favorites->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
