<?php

namespace App\Services\ExternalFootball;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class FootballDataClient
{
    public function configStatus(): array
    {
        $provider = config('services.external_football.provider');
        $baseUrl = config('services.external_football.base_url');
        $token = (string) config('services.external_football.token');
        $timeout = config('services.external_football.timeout', 15);

        return [
            'provider' => $provider,
            'base_url_configured' => filled($baseUrl),
            'token_configured' => $token !== '',
            'token_length' => strlen($token),
            'timeout' => (int) $timeout,
            'competition_code' => 'WC',
        ];
    }

    public function getCompetitions(): array
    {
        return $this->get('/competitions');
    }

    public function getWorldCup(): array
    {
        return $this->get('/competitions/WC');
    }

    public function getWorldCupMatches(): array
    {
        return $this->get('/competitions/WC/matches');
    }

    public function getWorldCupTeams(): array
    {
        return $this->get('/competitions/WC/teams');
    }

    public function getTeam(int $teamId): array
    {
        return $this->get('/teams/'.$teamId);
    }

    protected function get(string $path): array
    {
        $provider = (string) config('services.external_football.provider', 'football-data');
        $baseUrl = rtrim((string) config('services.external_football.base_url'), '/');
        $token = (string) config('services.external_football.token');
        $timeout = (int) config('services.external_football.timeout', 15);

        if ($provider !== 'football-data') {
            return $this->failure(null, 'External football provider is not football-data.');
        }

        if ($baseUrl === '' || $token === '') {
            return $this->failure(null, 'External football API is not fully configured.');
        }

        try {
            $response = Http::withHeaders(['X-Auth-Token' => $token])
                ->acceptJson()
                ->timeout($timeout)
                ->get($baseUrl.$path);
        } catch (ConnectionException $exception) {
            return $this->failure(null, 'External football API connection failed.');
        }

        $data = null;

        try {
            $data = $response->json();
        } catch (\Throwable) {
            $data = null;
        }

        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'data' => is_array($data) ? $data : [],
            'error' => $response->ok() ? null : $this->safeError($data),
            'rate_limit' => [
                'retry_after' => $response->header('Retry-After'),
                'requests_available_minute' => $response->header('X-Requests-Available-Minute'),
                'request_counter_reset' => $response->header('X-RequestCounter-Reset'),
            ],
        ];
    }

    protected function failure(?int $status, string $message): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'data' => [],
            'error' => $message,
            'rate_limit' => [],
        ];
    }

    protected function safeError(?array $data): string
    {
        $message = $data['message'] ?? $data['error'] ?? null;

        return is_string($message) && $message !== ''
            ? $message
            : 'External football API request failed.';
    }
}
