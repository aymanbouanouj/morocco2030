<?php

namespace App\Policies;

use App\Models\InterfaceTranslation;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class InterfaceTranslationPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'translations.manage');
    }

    public function view(User $user, InterfaceTranslation $interfaceTranslation): Response
    {
        return $this->allowIfPermission($user, 'translations.manage');
    }

    public function update(User $user, InterfaceTranslation $interfaceTranslation): Response
    {
        return $this->allowIfPermission($user, 'translations.manage');
    }
}
