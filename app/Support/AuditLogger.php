<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public static function log(
        Request $request,
        Model $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        if (! $request->user()) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $request->user()->id,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'old_values' => self::redactSensitiveValues($oldValues),
            'new_values' => self::redactSensitiveValues($newValues),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route_name' => optional($request->route())->getName(),
            'occurred_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>|null
     */
    public static function redactSensitiveValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return collect($values)
            ->mapWithKeys(function (mixed $value, string|int $key): array {
                if (self::isSensitiveKey((string) $key)) {
                    return [$key => '[redacted]'];
                }

                if (is_array($value)) {
                    return [$key => self::redactSensitiveValues($value)];
                }

                return [$key => $value];
            })
            ->all();
    }

    protected static function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower($key);

        return str_contains($normalized, 'password')
            || str_contains($normalized, 'token')
            || str_contains($normalized, 'api_key')
            || str_contains($normalized, 'secret')
            || in_array($normalized, ['app_key', 'db_password', 'remember_token'], true);
    }
}
