<?php

namespace App\Policies;

use App\Models\NewsCategory;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class NewsCategoryPolicy
{
    use ChecksAdminPermissions;

    /**
     * Category taxonomy is editorial-management scope (chief editor / platform admin),
     * not general article drafting (journalist with news.manage only).
     */
    private function manageNewsCategories(User $user): Response
    {
        return $this->allowIfPermission($user, ['news.review', 'news.publish']);
    }

    public function viewAny(User $user): Response
    {
        return $this->manageNewsCategories($user);
    }

    public function view(User $user, NewsCategory $newsCategory): Response
    {
        return $this->manageNewsCategories($user);
    }

    public function create(User $user): Response
    {
        return $this->manageNewsCategories($user);
    }

    public function update(User $user, NewsCategory $newsCategory): Response
    {
        return $this->manageNewsCategories($user);
    }

    public function delete(User $user, NewsCategory $newsCategory): Response
    {
        return $this->manageNewsCategories($user);
    }
}
