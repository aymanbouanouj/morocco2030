<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use App\Support\NewsWorkflowManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsWorkflowController extends AdminController
{
    public function __construct(protected NewsWorkflowManager $workflowManager)
    {
    }

    public function submitForReview(Request $request, News $news): RedirectResponse
    {
        $this->authorize('submitForReview', $news);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $news->status;
        $news = $this->workflowManager->submitForReview($news, $request->user(), $validated['notes'] ?? null);

        $this->recordAudit($request, $news, 'news.submitted_for_review', ['status' => $previousStatus], ['status' => $news->status]);

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'News item submitted for review.');
    }

    public function approve(Request $request, News $news): RedirectResponse
    {
        $this->authorize('approve', $news);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $news->status;
        $news = $this->workflowManager->approve($news, $request->user(), $validated['notes'] ?? null);

        $this->recordAudit($request, $news, 'news.approved', ['status' => $previousStatus], ['status' => $news->status]);

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'News item approved.');
    }

    public function reject(Request $request, News $news): RedirectResponse
    {
        $this->authorize('reject', $news);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $news->status;
        $news = $this->workflowManager->reject($news, $request->user(), $validated['notes'] ?? null);

        $this->recordAudit($request, $news, 'news.rejected', ['status' => $previousStatus], ['status' => $news->status]);

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'News item rejected.');
    }

    public function publish(Request $request, News $news): RedirectResponse
    {
        $this->authorize('publish', $news);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $news->status;
        $news = $this->workflowManager->publish($news, $request->user(), $validated['notes'] ?? null);

        $this->recordAudit($request, $news, 'news.published', ['status' => $previousStatus], ['status' => $news->status]);

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'News item published.');
    }

    public function archive(Request $request, News $news): RedirectResponse
    {
        $this->authorize('archive', $news);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $news->status;
        $news = $this->workflowManager->archive($news, $request->user(), $validated['notes'] ?? null);

        $this->recordAudit($request, $news, 'news.archived', ['status' => $previousStatus], ['status' => $news->status]);

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'News item archived.');
    }
}
