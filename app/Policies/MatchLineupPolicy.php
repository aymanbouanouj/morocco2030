<?php

namespace App\Policies;

use App\Models\MatchLineup;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class MatchLineupPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function view(User $user, MatchLineup $matchLineup): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function update(User $user, MatchLineup $matchLineup): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function delete(User $user, MatchLineup $matchLineup): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }
}
