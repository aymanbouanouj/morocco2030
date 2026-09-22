<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class PartnerPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'partners.manage');
    }

    public function view(User $user, Partner $partner): Response
    {
        return $this->allowIfPermission($user, 'partners.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'partners.manage');
    }

    public function update(User $user, Partner $partner): Response
    {
        return $this->allowIfPermission($user, 'partners.manage');
    }

    public function delete(User $user, Partner $partner): Response
    {
        return $this->allowIfPermission($user, 'partners.manage');
    }
}
