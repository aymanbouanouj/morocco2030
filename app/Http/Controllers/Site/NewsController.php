<?php

namespace App\Http\Controllers\Site;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends SiteController
{
    public function index(Request $request): View
    {
        $newsItems = $this->publishedPublicNewsQuery()
            ->with(['category.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
            ->when($request->string('category')->toString(), function ($query, $category) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
            })
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('public.news.index', [
            'newsItems' => $newsItems,
        ]);
    }

    public function show(string $slug): View
    {
        $news = $this->publishedPublicNewsQuery()
            ->with([
                'category.translations.language',
                'author',
                'mediaRelations.mediaFile',
                'translations.language',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedNews = $this->publishedPublicNewsQuery()
            ->with(['category.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
            ->whereKeyNot($news->id)
            ->where(function ($query) use ($news) {
                $query->where('category_id', $news->category_id)
                    ->orWhere('author_id', $news->author_id);
            })
            ->orderByDesc('published_at')
            ->latest('id')
            ->take(3)
            ->get();

        return view('public.news.show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
        ]);
    }
}
