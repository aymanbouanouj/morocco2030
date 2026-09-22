<?php

namespace App\Policies;

use App\Models\Standing;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class StandingPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, ['standings.manage', 'matches.manage']);
    }

    public function view(User $user, Standing $standing): Response
    {
        return $this->allowIfPermission($user, ['standings.manage', 'matches.manage']);
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'standings.manage');
    }

    public function update(User $user, Standing $standing): Response
    {
        return $this->allowIfPermission($user, 'standings.manage');
    }

    public function delete(User $user, Standing $standing): Response
    {
        return $this->allowIfPermission($user, 'standings.manage');
    }

    public function recalculate(User $user): Response
    {
        return $this->allowIfPermission($user, 'standings.manage');
    }
}
