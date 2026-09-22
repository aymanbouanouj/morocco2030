<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\News\StoreNewsRequest;
use App\Http\Requests\Admin\News\UpdateNewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use App\Support\NewsMediaStorage;
use App\Support\NewsWorkflowManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends AdminController
{
    public function __construct(
        protected NewsWorkflowManager $workflowManager,
        protected NewsMediaStorage $newsMediaStorage,
    ) {
        $this->authorizeResource(News::class, 'news');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $canReviewAll = $user->hasAnyPermission(['news.review', 'news.publish']);

        $scopedQuery = fn (): Builder => News::query()
            ->when(! $canReviewAll, fn (Builder $builder) => $builder->where('author_id', $user->id));

        $query = $scopedQuery()
            ->with(['category', 'author', 'editor'])
            ->when($request->string('search')->toString(), function ($builder, $search) {
                $builder->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($builder, $status) => $builder->where('status', $status))
            ->when($request->integer('category_id'), fn ($builder, $categoryId) => $builder->where('category_id', $categoryId))
            ->when($canReviewAll && $request->integer('author_id'), fn ($builder, $authorId) => $builder->where('author_id', $authorId))
            ->latest();

        $workflowCounts = [
            'draft' => $scopedQuery()->where('status', 'draft')->count(),
            'pending_review' => $scopedQuery()->where('status', 'pending_review')->count(),
            'published' => $scopedQuery()->where('status', 'published')->count(),
            'scheduled' => $scopedQuery()
                ->whereIn('status', ['approved', 'published'])
                ->whereNotNull('published_at')
                ->where('published_at', '>', now())
                ->count(),
            'archived' => $scopedQuery()->where('status', 'archived')->count(),
        ];

        $authors = $canReviewAll
            ? User::query()
                ->whereIn('id', News::query()->select('author_id')->distinct())
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();

        $filters = $request->only(['search', 'status', 'category_id', 'author_id']);
        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        return view('admin.news.index', [
            'newsItems' => $query->paginate(15)->withQueryString(),
            'categories' => NewsCategory::query()->orderBy('name')->get(),
            'authors' => $authors,
            'filters' => $filters,
            'hasActiveFilters' => $hasActiveFilters,
            'workflowCounts' => $workflowCounts,
            'canCreateNews' => $user->can('create', News::class),
        ]);
    }

    public function create(): View
    {
        return view('admin.news.create', [
            'categories' => NewsCategory::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $news = News::query()->create([
            ...$this->newsContentAttributes($request),
            'author_id' => $request->user()->id,
            'status' => 'draft',
        ]);

        $this->newsMediaStorage->apply($request, $news, isUpdate: false);

        $this->workflowManager->recordDraftCreation($news, $request->user());
        $this->recordAudit($request, $news, 'news.created', null, $news->fresh()->toArray());

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'Draft created successfully.');
    }

    public function show(News $news): View
    {
        $this->authorize('view', $news);

        return view('admin.news.show', [
            'news' => $news->load([
                'category',
                'author',
                'editor',
                'editorialWorkflows.submittedBy',
                'editorialWorkflows.reviewedBy',
            ]),
        ]);
    }

    public function edit(News $news): View
    {
        return view('admin.news.edit', [
            'news' => $news,
            'categories' => NewsCategory::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $original = $news->toArray();
        $data = $this->newsContentAttributes($request);

        if ($request->user()->id !== $news->author_id) {
            $data['editor_id'] = $request->user()->id;
        }

        $news->update($data);
        $this->newsMediaStorage->apply($request, $news->fresh(), isUpdate: true);

        $this->recordAudit($request, $news, 'news.updated', $original, $news->fresh()->toArray());

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'News item updated successfully.');
    }

    public function destroy(Request $request, News $news): RedirectResponse
    {
        $original = $news->toArray();
        $news->delete();

        $this->recordAudit($request, $news, 'news.deleted', $original);

        return redirect()->route('admin.news.index')
            ->with('success', 'News item archived successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function newsContentAttributes(StoreNewsRequest|UpdateNewsRequest $request): array
    {
        return collect($request->validated())->only([
            'category_id',
            'title',
            'slug',
            'summary',
            'body',
            'visibility',
            'featured_at',
            'published_at',
        ])->all();
    }
}
