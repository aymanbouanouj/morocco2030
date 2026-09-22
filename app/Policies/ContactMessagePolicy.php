<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class ContactMessagePolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'contact-messages.manage');
    }

    public function view(User $user, ContactMessage $contactMessage): Response
    {
        return $this->allowIfPermission($user, 'contact-messages.manage');
    }

    public function update(User $user, ContactMessage $contactMessage): Response
    {
        return $this->allowIfPermission($user, 'contact-messages.manage');
    }
}
