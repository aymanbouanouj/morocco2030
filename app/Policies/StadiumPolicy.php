<?php

namespace App\Policies;

use App\Models\Stadium;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class StadiumPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'stadiums.manage');
    }

    public function view(User $user, Stadium $stadium): Response
    {
        return $this->allowIfPermission($user, 'stadiums.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'stadiums.manage');
    }

    public function update(User $user, Stadium $stadium): Response
    {
        return $this->allowIfPermission($user, 'stadiums.manage');
    }

    public function delete(User $user, Stadium $stadium): Response
    {
        return $this->allowIfPermission($user, 'stadiums.manage');
    }
}
