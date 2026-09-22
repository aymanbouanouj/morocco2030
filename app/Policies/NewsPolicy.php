<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminPermissions;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    use ChecksAdminPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowIfPermission($user, ['news.manage', 'news.review', 'news.publish']);
    }

    public function view(User $user, News $news): Response
    {
        if ($user->hasAnyPermission(['news.review', 'news.publish'])) {
            return Response::allow();
        }

        return $user->hasPermission('news.manage') && $news->author_id === $user->id
            ? Response::allow()
            : Response::deny('You do not have access to this news item.');
    }

    public function create(User $user): Response
    {
        return $this->allowIfPermission($user, 'news.manage');
    }

    public function update(User $user, News $news): Response
    {
        if ($user->hasAnyPermission(['news.review', 'news.publish'])) {
            return Response::allow();
        }

        return $user->hasPermission('news.manage')
            && $news->author_id === $user->id
            && in_array($news->status, ['draft', 'rejected'], true)
                ? Response::allow()
                : Response::deny('Only draft or rejected items can be edited by their author.');
    }

    public function delete(User $user, News $news): Response
    {
        if ($user->hasAnyPermission(['news.review', 'news.publish'])) {
            return Response::allow();
        }

        return $user->hasPermission('news.manage')
            && $news->author_id === $user->id
            && in_array($news->status, ['draft', 'rejected', 'archived'], true)
                ? Response::allow()
                : Response::deny('Only draft, rejected, or archived items can be removed by their author.');
    }

    public function submitForReview(User $user, News $news): Response
    {
        return $user->hasPermission('news.manage')
            && $news->author_id === $user->id
            && in_array($news->status, ['draft', 'rejected'], true)
                ? Response::allow()
                : Response::deny('This news item cannot be submitted for review.');
    }

    public function approve(User $user, News $news): Response
    {
        return $user->hasAnyPermission(['news.review', 'news.publish']) && $news->status === 'pending_review'
            ? Response::allow()
            : Response::deny('Only pending news can be approved.');
    }

    public function reject(User $user, News $news): Response
    {
        return $user->hasAnyPermission(['news.review', 'news.publish']) && $news->status === 'pending_review'
            ? Response::allow()
            : Response::deny('Only pending news can be rejected.');
    }

    public function publish(User $user, News $news): Response
    {
        return $user->hasPermission('news.publish') && $news->status === 'approved'
            ? Response::allow()
            : Response::deny('Only approved news can be published.');
    }

    public function archive(User $user, News $news): Response
    {
        return $user->hasPermission('news.publish') && $news->status !== 'archived'
            ? Response::allow()
            : Response::deny('This news item cannot be archived.');
    }
}
