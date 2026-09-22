<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\VisitorAnalytic;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackVisitorAnalytics
{
    /**
     * @var array<int, string>|null
     */
    private static ?array $columns = null;

    /**
     * Track public page views after the response is generated.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $columns = $this->visitorAnalyticsColumns();

            if ($columns === []) {
                return $response;
            }

            VisitorAnalytic::query()->create($this->payload($request, $columns));
        } catch (Throwable) {
            // Analytics must never block public page rendering.
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if (! $response->isSuccessful()) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if ($contentType !== '' && ! str_contains(Str::lower($contentType), 'text/html')) {
            return false;
        }

        $path = trim($request->path(), '/');

        if ($this->isExcludedPath($path)) {
            return false;
        }

        return ! $this->hasAssetExtension($path);
    }

    private function isExcludedPath(string $path): bool
    {
        $exact = [
            'login',
            'register',
            'forgot-password',
            'logout',
            'favicon.ico',
            'robots.txt',
            'up',
        ];

        if (in_array($path, $exact, true)) {
            return true;
        }

        foreach (['admin', 'assets', 'build', 'reset-password', 'storage', 'vendor'] as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    private function hasAssetExtension(string $path): bool
    {
        return (bool) preg_match('/\.(?:css|js|map|json|xml|txt|png|jpe?g|gif|svg|webp|ico|avif|woff2?|ttf|eot)$/i', $path);
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<string, mixed>
     */
    private function payload(Request $request, array $columns): array
    {
        $payload = [
            'path' => $request->path() === '/' ? '/' : '/'.trim($request->path(), '/'),
            'event_type' => 'page_view',
            'event_at' => now(),
        ];

        $values = [
            'user_id' => $this->publicUserId($request),
            'session_id' => $request->hasSession()
                ? $this->pseudonymize($request->session()->getId())
                : null,
            'route_name' => $request->route()?->getName(),
            'ip_address' => $this->pseudonymize($request->ip()),
            'language_code' => $this->truncate(app()->getLocale(), 10),
            'device_type' => $this->deviceType($request->userAgent()),
            'meta' => [
                'tracker' => 'server_middleware',
                'ip_pseudonymized' => true,
                'session_pseudonymized' => true,
                'referrer_stored' => false,
                'user_agent_stored' => false,
            ],
        ];

        foreach ($values as $column => $value) {
            if (in_array($column, $columns, true) && $value !== null) {
                $payload[$column] = $value;
            }
        }

        return $payload;
    }

    private function publicUserId(Request $request): ?int
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->canAccessPublicAccount()) {
            return null;
        }

        return (int) $user->getKey();
    }

    private function pseudonymize(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $salt = (string) config('app.key');

        if ($salt === '') {
            return null;
        }

        return substr(hash_hmac('sha256', $value, $salt), 0, 40);
    }

    private function deviceType(?string $userAgent): string
    {
        $agent = Str::lower((string) $userAgent);

        if ($agent === '') {
            return 'unknown';
        }

        if (str_contains($agent, 'bot') || str_contains($agent, 'crawler') || str_contains($agent, 'spider')) {
            return 'bot';
        }

        if (str_contains($agent, 'ipad') || str_contains($agent, 'tablet')) {
            return 'tablet';
        }

        if (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function truncate(?string $value, int $limit): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Str::limit($value, $limit, '');
    }

    /**
     * @return array<int, string>
     */
    private function visitorAnalyticsColumns(): array
    {
        if (self::$columns !== null) {
            return self::$columns;
        }

        try {
            if (! Schema::hasTable('visitor_analytics')) {
                return self::$columns = [];
            }

            return self::$columns = Schema::getColumnListing('visitor_analytics');
        } catch (Throwable) {
            return self::$columns = [];
        }
    }
}
