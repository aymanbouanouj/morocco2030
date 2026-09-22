<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'teams.manage');
    }

    public function view(User $user, Team $team): Response
    {
        return $this->allowIfPermission($user, 'teams.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'teams.manage');
    }

    public function update(User $user, Team $team): Response
    {
        return $this->allowIfPermission($user, 'teams.manage');
    }

    public function delete(User $user, Team $team): Response
    {
        return $this->allowIfPermission($user, 'teams.manage');
    }
}
