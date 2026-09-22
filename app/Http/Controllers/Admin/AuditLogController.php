<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AuditLogController extends AdminController
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AuditLog::class);

        $logs = AuditLog::query()
            ->with('user')
            ->when($request->integer('user_id'), fn (Builder $query, int $userId) => $query->where('user_id', $userId))
            ->when($request->string('action')->toString(), fn (Builder $query, string $action) => $query->where('action', $action))
            ->when($request->string('auditable_type')->toString(), fn (Builder $query, string $type) => $query->where('auditable_type', $type))
            ->when($request->integer('auditable_id'), fn (Builder $query, int $id) => $query->where('auditable_id', $id))
            ->when($this->dateFilter($request, 'date_from'), fn (Builder $query, string $date) => $query->whereDate('occurred_at', '>=', $date))
            ->when($this->dateFilter($request, 'date_to'), fn (Builder $query, string $date) => $query->whereDate('occurred_at', '<=', $date))
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('route_name', 'like', "%{$search}%")
                        ->orWhere('auditable_type', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('occurred_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'filters' => $request->only([
                'user_id',
                'action',
                'auditable_type',
                'auditable_id',
                'date_from',
                'date_to',
                'search',
            ]),
            'users' => User::query()
                ->whereIn('id', AuditLog::query()->select('user_id')->whereNotNull('user_id'))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
            'auditableTypes' => AuditLog::query()->select('auditable_type')->distinct()->orderBy('auditable_type')->pluck('auditable_type'),
            'summary' => $this->summary(),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $this->authorize('view', $auditLog);

        $auditLog->load(['user', 'auditable']);

        return view('admin.audit-logs.show', [
            'auditLog' => $auditLog,
            'oldValues' => AuditLogger::redactSensitiveValues($auditLog->old_values),
            'newValues' => AuditLogger::redactSensitiveValues($auditLog->new_values),
            'oldValuesJson' => $this->formatValues(AuditLogger::redactSensitiveValues($auditLog->old_values)),
            'newValuesJson' => $this->formatValues(AuditLogger::redactSensitiveValues($auditLog->new_values)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(): array
    {
        $mostActive = AuditLog::query()
            ->selectRaw('user_id, COUNT(*) as total')
            ->with('user')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->first();

        return [
            'total' => AuditLog::query()->count(),
            'today' => AuditLog::query()->where('occurred_at', '>=', now()->startOfDay())->count(),
            'latestAction' => AuditLog::query()->latest('occurred_at')->value('action'),
            'mostActiveUser' => $mostActive?->user?->name,
            'mostActiveCount' => (int) ($mostActive?->total ?? 0),
        ];
    }

    private function dateFilter(Request $request, string $key): ?string
    {
        $value = $request->string($key)->toString();

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>|null  $values
     */
    private function formatValues(?array $values): string
    {
        if ($values === null || $values === []) {
            return 'No values recorded.';
        }

        return json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ?: 'Unable to format values.';
    }
}
