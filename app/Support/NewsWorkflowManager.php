<?php

namespace App\Support;

use App\Models\EditorialWorkflow;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NewsWorkflowManager
{
    public function recordDraftCreation(News $news, User $actor): void
    {
        $this->recordWorkflow($news, $actor, 'draft', 'created', [
            'new_status' => 'draft',
        ]);
    }

    public function submitForReview(News $news, User $actor, ?string $notes = null): News
    {
        return $this->transition($news, $actor, 'pending_review', 'submit_for_review', $notes);
    }

    public function approve(News $news, User $actor, ?string $notes = null): News
    {
        return $this->transition($news, $actor, 'approved', 'approve', $notes);
    }

    public function reject(News $news, User $actor, ?string $notes = null): News
    {
        return $this->transition($news, $actor, 'rejected', 'reject', $notes);
    }

    public function publish(News $news, User $actor, ?string $notes = null): News
    {
        return $this->transition($news, $actor, 'published', 'publish', $notes);
    }

    public function archive(News $news, User $actor, ?string $notes = null): News
    {
        return $this->transition($news, $actor, 'archived', 'archive', $notes);
    }

    protected function transition(News $news, User $actor, string $newStatus, string $step, ?string $notes = null): News
    {
        return DB::transaction(function () use ($actor, $newStatus, $news, $notes, $step) {
            $originalStatus = $news->status;

            $news->status = $newStatus;

            if (in_array($newStatus, ['approved', 'rejected', 'published', 'archived'], true)) {
                $news->editor_id = $actor->id;
            }

            if ($newStatus === 'published') {
                $news->published_at = $news->published_at ?? now();
            }

            if (in_array($newStatus, ['draft', 'pending_review', 'rejected', 'archived'], true)) {
                $news->published_at = $newStatus === 'archived' ? null : $news->published_at;
            }

            $news->save();

            $this->recordWorkflow($news, $actor, $newStatus, $step, [
                'previous_status' => $originalStatus,
                'new_status' => $newStatus,
            ], $notes);

            return $news->fresh();
        });
    }

    protected function recordWorkflow(
        News $news,
        User $actor,
        string $status,
        string $step,
        array $payload = [],
        ?string $notes = null
    ): void {
        EditorialWorkflow::query()->create([
            'workflowable_type' => News::class,
            'workflowable_id' => $news->id,
            'submitted_by' => in_array($step, ['created', 'submit_for_review'], true) ? $actor->id : $news->author_id,
            'reviewed_by' => in_array($step, ['approve', 'reject', 'publish', 'archive'], true) ? $actor->id : null,
            'status' => $status,
            'current_step' => $step,
            'notes' => $notes,
            'payload' => $payload,
            'submitted_at' => $step === 'submit_for_review' ? now() : null,
            'reviewed_at' => in_array($step, ['approve', 'reject', 'publish', 'archive'], true) ? now() : null,
            'published_at' => $status === 'published' ? $news->published_at : null,
        ]);
    }
}
