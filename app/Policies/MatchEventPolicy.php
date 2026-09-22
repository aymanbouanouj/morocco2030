<?php

namespace App\Policies;

use App\Models\MatchEvent;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class MatchEventPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function view(User $user, MatchEvent $matchEvent): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function update(User $user, MatchEvent $matchEvent): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function delete(User $user, MatchEvent $matchEvent): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }
}
