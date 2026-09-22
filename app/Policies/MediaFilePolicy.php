<?php

namespace App\Policies;

use App\Models\MediaFile;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class MediaFilePolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function view(User $user, MediaFile $mediaFile): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function attach(User $user, MediaFile $mediaFile): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function detach(User $user, MediaFile $mediaFile): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function archive(User $user, MediaFile $mediaFile): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function restore(User $user, MediaFile $mediaFile): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function update(User $user, MediaFile $mediaFile): Response
    {
        return $this->allowIfPermission($user, 'media.manage');
    }

    public function delete(User $user, MediaFile $mediaFile): Response
    {
        return Response::deny('Media deletion is not enabled in this phase.');
    }
}
