<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class SettingPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'settings.manage');
    }

    public function view(User $user, Setting $setting): Response
    {
        return $this->allowIfPermission($user, 'settings.manage');
    }

    public function update(User $user, Setting $setting): Response
    {
        if ($setting->isSensitive()) {
            return Response::deny('Sensitive configuration values cannot be edited from the admin UI.');
        }

        return $this->allowIfPermission($user, 'settings.manage');
    }
}
