<?php

namespace App\Policies;

use App\Models\MatchStatistic;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class MatchStatisticPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function view(User $user, MatchStatistic $matchStatistic): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function update(User $user, MatchStatistic $matchStatistic): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }

    public function delete(User $user, MatchStatistic $matchStatistic): Response
    {
        return $this->allowIfPermission($user, 'matches.manage');
    }
}
