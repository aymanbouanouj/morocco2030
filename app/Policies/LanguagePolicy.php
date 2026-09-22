<?php

namespace App\Policies;

use App\Models\Language;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class LanguagePolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'languages.manage');
    }

    public function view(User $user, Language $language): Response
    {
        return $this->allowIfPermission($user, 'languages.manage');
    }

    public function update(User $user, Language $language): Response
    {
        return $this->allowIfPermission($user, 'languages.manage');
    }
}
