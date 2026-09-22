<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Auth\Access\Response;

trait ChecksAdminPermissions
{
    protected function allowIfPermission(User $user, string|array $permissions): Response
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        return $user->hasAnyPermission($permissions)
            ? Response::allow()
            : Response::deny('You do not have the required permission for this action.');
    }
}
