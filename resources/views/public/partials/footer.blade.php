<footer class="m2030-footer-final" role="contentinfo">
    <div class="m2030-footer-final__pattern" aria-hidden="true"></div>

    <div class="m2030-footer-final__inner">
        <div class="m2030-footer-final__top">
            <section class="m2030-footer-final__brand" aria-label="MOROCCO 2030">
                <img
                    class="m2030-footer-final__logo"
                    src="{{ asset('assets/brand/logo.svg') }}"
                    alt="MOROCCO 2030"
                    width="48"
                    height="48"
                    decoding="async"
                >
                <p class="m2030-footer-final__slogan">
                    {{ __('UNITING PASSIONS.') }}<br>
                    {{ __('INSPIRING GENERATIONS.') }}
                </p>
            </section>

            <section class="m2030-footer-final__newsletter" aria-labelledby="footer-newsletter-title">
                <h2 id="footer-newsletter-title">{{ __('Newsletter') }}</h2>
                <p>{{ __('Newsletter signup is disabled in this publication build.') }}</p>
            </section>

            <section class="m2030-footer-final__follow" aria-labelledby="footer-follow-title">
                <div class="m2030-footer-final__follow-title">
                    <span aria-hidden="true"></span>
                    <h2 id="footer-follow-title">{{ __('Follow Us') }}</h2>
                    <span aria-hidden="true"></span>
                </div>
                <div class="m2030-footer-final__social">
                    <span class="m2030-footer-final__social-link" role="img" aria-label="{{ __('Facebook') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M14 8.5h2.5l-.5 2.8H14v8.2h-3V11.3H9.5V8.5H11V6.4c0-1.2.4-2.3 1.2-3.1.8-.8 1.9-1.2 3.1-1.2H14v2.8h-1.1c-.5 0-.9.4-.9.9V8.5Z"/>
                        </svg>
                    </span>
                    <span class="m2030-footer-final__social-link" role="img" aria-label="X">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M14.3 4h2.7l-5.9 6.7L18 20h-5.1l-4-5.2-4.6 5.2H1.7l6.3-7.2L2 4h5.2l3.6 4.8L14.3 4Zm-.9 14.2h1.5L7.1 5.7H5.5l7.9 12.5Z"/>
                        </svg>
                    </span>
                    <span class="m2030-footer-final__social-link" role="img" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M8 4h8a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4Zm0 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H8Zm4 2.2A3.8 3.8 0 1 1 8.2 12 3.8 3.8 0 0 1 12 8.2Zm0 2A1.8 1.8 0 1 0 13.8 12 1.8 1.8 0 0 0 12 10.2ZM16.8 6.6a1 1 0 1 1-1 1 1 1 0 0 1 1-1Z"/>
                        </svg>
                    </span>
                    <span class="m2030-footer-final__social-link" role="img" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M10 9.2v5.6l4.8-2.8L10 9.2ZM21 8.2s-.2-1.4-.8-2c-.8-.9-1.7-.9-2.1-1C15.8 5 12 5 12 5h0s-3.8 0-6.1.2c-.4 0-1.3.1-2.1 1-.6.6-.8 2-.8 2S3 9.6 3 11v1.8c0 1.4.2 2.8.2 2.8s.2 1.4.8 2c.8.9 1.8.9 2.2 1 1.6.2 6.8.2 6.8.2s3.8 0 6.1-.2c.4 0 1.3-.1 2.1-1 .6-.6.8-2 .8-2S21 14.4 21 13V11c0-1.4-.2-2.8-.2-2.8Z"/>
                        </svg>
                    </span>
                    <span class="m2030-footer-final__social-link" role="img" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M14.5 4.2c.4 1.6 1.4 2.8 3 3.4v2.4c-1.1 0-2.1-.3-3-.9v5.9a4.8 4.8 0 1 1-4.8-4.8c.2 0 .5 0 .7.1v2.6a2.2 2.2 0 1 0 1.6 2.1V4.2h1.5Z"/>
                        </svg>
                    </span>
                </div>
            </section>
        </div>

        <div class="m2030-footer-final__divider" aria-hidden="true">
            <span></span>
        </div>

        <nav class="m2030-footer-final__nav" aria-label="{{ __('Footer navigation') }}">
            <section>
                <h3>{{ __('Tournament') }}</h3>
                <a href="{{ route('home') }}">{{ __('About Morocco 2030') }}</a>
                <span class="m2030-footer-final__nav-muted">{{ __('Vision & Legacy') }}</span>
                <span class="m2030-footer-final__nav-muted">{{ __('Sustainability') }}</span>
                <a href="{{ route('cities.index') }}">{{ __('Host Cities') }}</a>
            </section>

            <section>
                <h3>{{ __('Information') }}</h3>
                <span class="m2030-footer-final__nav-muted">{{ __('Tickets') }}</span>
                <span class="m2030-footer-final__nav-muted">{{ __('Fan Guide') }}</span>
                <span class="m2030-footer-final__nav-muted">{{ __('Accreditation') }}</span>
                <a href="{{ route('news.index') }}">{{ __('Media Centre') }}</a>
            </section>

            <section>
                <h3>{{ __('Discover') }}</h3>
                <a href="{{ route('cities.index') }}">{{ __('Host Cities') }}</a>
                <a href="{{ route('stadiums.index') }}">{{ __('Stadiums') }}</a>
                <span class="m2030-footer-final__nav-muted">{{ __('Culture') }}</span>
                <a href="{{ route('news.index') }}">{{ __('News') }}</a>
            </section>

            <section>
                <h3>{{ __('Support') }}</h3>
                <span class="m2030-footer-final__nav-muted">{{ __('Help Centre') }}</span>
                <span class="m2030-footer-final__nav-muted">{{ __('Contact Us') }}</span>
                <a href="{{ route('public.privacy') }}">{{ __('Privacy Policy') }}</a>
                <a href="{{ route('public.terms') }}">{{ __('Terms of Use') }}</a>
            </section>
        </nav>
    </div>

    <div class="m2030-footer-final__bottom">
        <p>&copy; {{ now()->year }} {{ config('app.name', 'MOROCCO 2030') }}. {{ __('All rights reserved to Ayman Bounaouj.') }}</p>
    </div>
</footer>
