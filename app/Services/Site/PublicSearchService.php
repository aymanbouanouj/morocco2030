<?php

namespace App\Services\Site;

use App\Models\City;
use App\Models\MatchFixture;
use App\Models\News;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;

class PublicSearchService
{
    public function search(string $term): array
    {
        $term = trim($term);

        if ($term === '') {
            return $this->emptyResults();
        }

        $like = '%'.$term.'%';

        return [
            'news' => $this->searchNews($like),
            'matches' => $this->searchMatches($like),
            'teams' => $this->searchTeams($like),
            'players' => $this->searchPlayers($like),
            'cities' => $this->searchCities($like),
            'stadiums' => $this->searchStadiums($like),
            'partners' => $this->searchPartners($like),
        ];
    }

    protected function searchNews(string $like)
    {
        return News::query()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where(function (Builder $query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('body', 'like', $like)
                    ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', $like));

                $this->applyTranslationSearch($query, ['title', 'summary', 'body'], $like);
            })
            ->with(['category', 'mediaRelations.mediaFile', 'translations.language'])
            ->orderByDesc('published_at')
            ->latest('id')
            ->take(6)
            ->get();
    }

    protected function searchMatches(string $like)
    {
        return MatchFixture::query()
            ->where(function (Builder $query) use ($like) {
                $query->where('code', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhereHas('homeTeam', fn (Builder $teamQuery) => $teamQuery->where('name', 'like', $like)->orWhere('code', 'like', $like))
                    ->orWhereHas('awayTeam', fn (Builder $teamQuery) => $teamQuery->where('name', 'like', $like)->orWhere('code', 'like', $like))
                    ->orWhereHas('city', fn (Builder $cityQuery) => $cityQuery->where('name', 'like', $like)->orWhere('code', 'like', $like))
                    ->orWhereHas('stadium', fn (Builder $stadiumQuery) => $stadiumQuery->where('name', 'like', $like)->orWhere('code', 'like', $like))
                    ->orWhereHas('group', fn (Builder $groupQuery) => $groupQuery->where('name', 'like', $like)->orWhere('code', 'like', $like));
            })
            ->with([
                'homeTeam.translations.language',
                'awayTeam.translations.language',
                'city.translations.language',
                'stadium.translations.language',
                'group',
                'targetProgressions.sourceMatch',
            ])
            ->orderByDesc('match_date')
            ->take(6)
            ->get();
    }

    protected function searchTeams(string $like)
    {
        return Team::query()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('short_name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('coach_name', 'like', $like)
                    ->orWhere('federation_name', 'like', $like)
                    ->orWhereHas('group', fn (Builder $groupQuery) => $groupQuery->where('name', 'like', $like)->orWhere('code', 'like', $like));

                $this->applyTranslationSearch($query, ['name', 'description'], $like);
            })
            ->with(['group', 'mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    protected function searchPlayers(string $like)
    {
        return Player::query()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($like) {
                $query->where('display_name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('position', 'like', $like)
                    ->orWhere('bio', 'like', $like)
                    ->orWhereHas('team', fn (Builder $teamQuery) => $teamQuery->where('name', 'like', $like)->orWhere('code', 'like', $like));

                $this->applyTranslationSearch($query, ['display_name', 'bio', 'club'], $like);
            })
            ->with(['team.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('display_name')
            ->take(6)
            ->get();
    }

    protected function searchCities(string $like)
    {
        return City::query()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('region', 'like', $like)
                    ->orWhere('description', 'like', $like);

                $this->applyTranslationSearch($query, ['name', 'description'], $like);
            })
            ->withCount(['stadiums', 'matches'])
            ->with(['mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    protected function searchStadiums(string $like)
    {
        return Stadium::query()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhereHas('city', fn (Builder $cityQuery) => $cityQuery->where('name', 'like', $like)->orWhere('code', 'like', $like));

                $this->applyTranslationSearch($query, ['name', 'description'], $like);
            })
            ->with(['city.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    protected function searchPartners(string $like)
    {
        return Partner::query()
            ->where('status', 'active')
            ->where(function (Builder $query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('category', 'like', $like)
                    ->orWhere('tier', 'like', $like)
                    ->orWhere('description', 'like', $like);

                $this->applyTranslationSearch($query, ['name', 'description'], $like);
            })
            ->with(['mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    protected function applyTranslationSearch(Builder $query, array $fields, string $like): void
    {
        $query->orWhereHas('translations', function (Builder $translationQuery) use ($fields, $like) {
            $translationQuery->whereIn('field', $fields)
                ->where('value', 'like', $like);
        });
    }

    protected function emptyResults(): array
    {
        return [
            'news' => collect(),
            'matches' => collect(),
            'teams' => collect(),
            'players' => collect(),
            'cities' => collect(),
            'stadiums' => collect(),
            'partners' => collect(),
        ];
    }
}
