<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, ['users.view', 'users.manage']);
    }

    public function view(User $user, User $model): Response
    {
        return $this->allowIfPermission($user, ['users.view', 'users.manage']);
    }

    public function create(User $user): Response
    {
        if (! $user->hasRole('super-admin')) {
            return Response::deny('Only the super admin can create staff accounts.');
        }

        return Response::allow();
    }

    public function update(User $user, User $model): Response
    {
        return $this->allowIfPermission($user, 'users.manage');
    }

    public function delete(User $user, User $model): Response
    {
        return $this->allowIfPermission($user, 'users.manage');
    }

    public function assignRoles(User $user, User $model): Response
    {
        if (! $user->hasRole('super-admin')) {
            return Response::deny('Only the super admin can assign staff roles.');
        }

        if ($model->isPublic()) {
            return Response::deny('Public audience accounts cannot be assigned admin roles.');
        }

        return Response::allow();
    }
}
