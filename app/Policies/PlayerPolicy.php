<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class PlayerPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'players.manage');
    }

    public function view(User $user, Player $player): Response
    {
        return $this->allowIfPermission($user, 'players.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'players.manage');
    }

    public function update(User $user, Player $player): Response
    {
        return $this->allowIfPermission($user, 'players.manage');
    }

    public function delete(User $user, Player $player): Response
    {
        return $this->allowIfPermission($user, 'players.manage');
    }
}
