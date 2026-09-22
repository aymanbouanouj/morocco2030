<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class AuditLogPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->canViewAuditTrail($user);
    }

    public function view(User $user, AuditLog $auditLog): Response
    {
        return $this->canViewAuditTrail($user);
    }

    /**
     * Audit trail is limited to governance roles — not general editorial staff.
     */
    private function canViewAuditTrail(User $user): Response
    {
        if ($user->hasRole('super-admin')) {
            return Response::allow();
        }

        if (! $user->hasPermission('audit-logs.view')) {
            return Response::deny('You do not have the required permission for this action.');
        }

        if ($user->hasAnyRole(['platform-admin', 'analyst'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view audit logs.');
    }
}
