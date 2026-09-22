<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests\Site\Account\UpdateProfileRequest;
use App\Http\Requests\Site\Account\UpdateSettingsRequest;
use App\Models\City;
use App\Models\MatchFixture;
use App\Models\News;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Team;
use App\Models\User;
use App\Models\UserFavorite;
use App\Support\AuditLogger;
use App\Support\PublicContent;
use App\Support\PublicLocale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AccountController extends SiteController
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('public.account.index', [
            'user' => $user,
            'favoriteCount' => $user->favorites()->count(),
            'notificationCount' => $user->notifications()->count(),
            'unreadNotificationCount' => $user->notifications()->whereNull('read_at')->count(),
            'latestFavorites' => $this->presentFavorites(
                $user->favorites()->with('favorable')->latest()->limit(4)->get()
            ),
            'latestNotifications' => $user->notifications()
                ->latest('sent_at')
                ->latest('created_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('public.account.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $original = $user->only(['name', 'email', 'phone']);

        $user->fill($request->validated())->save();

        AuditLogger::log(
            $request,
            $user,
            'public-account.profile.updated',
            $original,
            $user->only(['name', 'email', 'phone'])
        );

        return redirect()->route('account.profile')
            ->with('success', __('Your profile has been updated.'));
    }

    public function settings(Request $request): View
    {
        return view('public.account.settings', [
            'user' => $request->user(),
        ]);
    }

    public function updateSettings(UpdateSettingsRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $original = $user->only(['preferred_locale']);
        $data = $request->validated();

        $preferredLocale = $data['preferred_locale'] ?? null;
        $passwordChanged = filled($data['password'] ?? null);

        $user->preferred_locale = $preferredLocale;

        if ($passwordChanged) {
            $user->password = $data['password'];
        }

        $user->save();

        if ($preferredLocale) {
            $request->session()->put(PublicLocale::SESSION_KEY, $preferredLocale);
        } else {
            $request->session()->forget(PublicLocale::SESSION_KEY);
        }

        AuditLogger::log(
            $request,
            $user,
            'public-account.settings.updated',
            $original,
            [
                'preferred_locale' => $user->preferred_locale,
                'password_changed' => $passwordChanged,
            ]
        );

        return redirect()->route('account.settings')
            ->with('success', __('Your account settings have been updated.'));
    }

    public function favorites(Request $request): View
    {
        $favorites = $request->user()
            ->favorites()
            ->with('favorable')
            ->latest()
            ->paginate(12)
            ->through(fn (UserFavorite $favorite) => $this->presentFavorite($favorite));

        return view('public.account.favorites', [
            'favorites' => $favorites,
        ]);
    }

    public function notifications(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest('sent_at')
            ->latest('created_at')
            ->paginate(12);

        return view('public.account.notifications', [
            'notifications' => $notifications,
            'unreadNotificationCount' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    /**
     * @param  Collection<int, UserFavorite>  $favorites
     * @return Collection<int, array<string, mixed>>
     */
    private function presentFavorites(Collection $favorites): Collection
    {
        return $favorites->map(fn (UserFavorite $favorite) => $this->presentFavorite($favorite));
    }

    /**
     * @return array<string, mixed>
     */
    private function presentFavorite(UserFavorite $favorite): array
    {
        $model = $favorite->favorable;

        return [
            'id' => $favorite->id,
            'created_at' => $favorite->created_at,
            'title' => $this->favoriteTitle($model),
            'type' => $this->favoriteTypeLabel($model),
            'summary' => $this->favoriteSummary($model),
            'url' => $this->favoriteUrl($model),
        ];
    }

    private function favoriteUrl(?Model $model): ?string
    {
        return match (true) {
            $model instanceof News => route('news.show', $model->slug),
            $model instanceof MatchFixture => route('matches.show', $model->slug),
            $model instanceof Team => route('teams.show', $model->slug),
            $model instanceof Player => route('players.show', $model->slug),
            $model instanceof City => route('cities.show', $model->slug),
            $model instanceof Stadium => route('stadiums.show', $model->slug),
            $model instanceof Partner => route('partners.index'),
            default => null,
        };
    }

    private function favoriteTitle(?Model $model): string
    {
        return match (true) {
            $model instanceof News => PublicContent::field($model, 'title') ?? $model->title,
            $model instanceof MatchFixture => $model->slotLabel('home').' vs '.$model->slotLabel('away'),
            $model instanceof Team => PublicContent::field($model, 'name') ?? $model->name,
            $model instanceof Player => PublicContent::field($model, 'display_name') ?? $model->display_name,
            $model instanceof City => PublicContent::field($model, 'name') ?? $model->name,
            $model instanceof Stadium => PublicContent::field($model, 'name') ?? $model->name,
            $model instanceof Partner => PublicContent::field($model, 'name') ?? $model->name,
            default => __('Saved item'),
        };
    }

    private function favoriteTypeLabel(?Model $model): string
    {
        return match (true) {
            $model instanceof News => __('News'),
            $model instanceof MatchFixture => __('Match'),
            $model instanceof Team => __('Team'),
            $model instanceof Player => __('Player'),
            $model instanceof City => __('City'),
            $model instanceof Stadium => __('Stadium'),
            $model instanceof Partner => __('Partner'),
            default => __('Saved item'),
        };
    }

    private function favoriteSummary(?Model $model): string
    {
        return match (true) {
            $model instanceof News => $model->summary ?: __('Published story'),
            $model instanceof MatchFixture => $model->stadium?->name
                ? __('Venue: :venue', ['venue' => PublicContent::field($model->stadium, 'name') ?? $model->stadium->name])
                : __('Fixture details'),
            $model instanceof Team => $model->coach_name
                ? __('Coach: :coach', ['coach' => $model->coach_name])
                : __('National team profile'),
            $model instanceof Player => $model->position
                ? __('Position: :position', ['position' => $model->position])
                : __('Squad player'),
            $model instanceof City => $model->region
                ? __('Region: :region', ['region' => $model->region])
                : __('Host city'),
            $model instanceof Stadium => $model->capacity
                ? __('Capacity: :capacity', ['capacity' => number_format($model->capacity)])
                : __('Host venue'),
            $model instanceof Partner => __('Official partner profile'),
            default => __('Saved to your account'),
        };
    }
}
