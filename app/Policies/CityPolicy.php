<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class CityPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, 'cities.manage');
    }

    public function view(User $user, City $city): Response
    {
        return $this->allowIfPermission($user, 'cities.manage');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'cities.manage');
    }

    public function update(User $user, City $city): Response
    {
        return $this->allowIfPermission($user, 'cities.manage');
    }

    public function delete(User $user, City $city): Response
    {
        return $this->allowIfPermission($user, 'cities.manage');
    }
}
