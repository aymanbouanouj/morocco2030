<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'roles.manage');
    }

    public function view(User $user, Role $role): Response
    {
        return $this->allowIfPermission($user, 'roles.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'roles.manage');
    }

    public function update(User $user, Role $role): Response
    {
        return $this->allowIfPermission($user, 'roles.manage');
    }

    public function delete(User $user, Role $role): Response
    {
        return $this->allowIfPermission($user, 'roles.manage');
    }
}
