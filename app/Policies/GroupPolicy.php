<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, [
            'groups.manage',
            'standings.manage',
            'matches.manage',
        ]);
    }

    public function view(User $user, Group $group): Response
    {
        return $this->allowIfPermission($user, 'groups.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'groups.manage');
    }

    public function update(User $user, Group $group): Response
    {
        return $this->allowIfPermission($user, 'groups.manage');
    }

    public function delete(User $user, Group $group): Response
    {
        return $this->allowIfPermission($user, 'groups.manage');
    }
}
