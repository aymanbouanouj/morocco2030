<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\NewsCategories\StoreNewsCategoryRequest;
use App\Http\Requests\Admin\NewsCategories\UpdateNewsCategoryRequest;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsCategoryController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(NewsCategory::class, 'newsCategory');
    }

    public function index(Request $request): View
    {
        $categories = NewsCategory::query()
            ->with('parent')
            ->withCount(['children', 'news'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.news-categories.index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.news-categories.create', [
            'categories' => NewsCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreNewsCategoryRequest $request): RedirectResponse
    {
        $category = NewsCategory::query()->create($request->validated());

        $this->recordAudit($request, $category, 'news_categories.created', null, $category->toArray());

        return redirect()->route('admin.news-categories.index')
            ->with('success', 'News category created successfully.');
    }

    public function edit(NewsCategory $newsCategory): View
    {
        return view('admin.news-categories.edit', [
            'newsCategory' => $newsCategory,
            'categories' => NewsCategory::query()
                ->whereKeyNot($newsCategory->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateNewsCategoryRequest $request, NewsCategory $newsCategory): RedirectResponse
    {
        $original = $newsCategory->toArray();
        $newsCategory->update($request->validated());

        $this->recordAudit($request, $newsCategory, 'news_categories.updated', $original, $newsCategory->fresh()->toArray());

        return redirect()->route('admin.news-categories.index')
            ->with('success', 'News category updated successfully.');
    }

    public function destroy(Request $request, NewsCategory $newsCategory): RedirectResponse
    {
        if ($newsCategory->children()->exists() || $newsCategory->news()->exists()) {
            return back()->with('error', 'Remove child categories and related news before deleting this category.');
        }

        $original = $newsCategory->toArray();
        $newsCategory->delete();

        $this->recordAudit($request, $newsCategory, 'news_categories.deleted', $original);

        return redirect()->route('admin.news-categories.index')
            ->with('success', 'News category archived successfully.');
    }
}
