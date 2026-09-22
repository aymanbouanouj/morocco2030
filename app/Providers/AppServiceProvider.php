<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\City;
use App\Models\ContactMessage;
use App\Models\Group;
use App\Models\InterfaceTranslation;
use App\Models\Language;
use App\Models\MatchEvent;
use App\Models\MatchFixture;
use App\Models\MatchLineup;
use App\Models\MatchStatistic;
use App\Models\MediaFile;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Stadium;
use App\Models\Standing;
use App\Models\Team;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\CityPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\GroupPolicy;
use App\Policies\InterfaceTranslationPolicy;
use App\Policies\LanguagePolicy;
use App\Policies\MatchEventPolicy;
use App\Policies\MatchFixturePolicy;
use App\Policies\MatchLineupPolicy;
use App\Policies\MatchStatisticPolicy;
use App\Policies\MediaFilePolicy;
use App\Policies\NewsCategoryPolicy;
use App\Policies\NewsPolicy;
use App\Policies\PartnerPolicy;
use App\Policies\PlayerPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\StadiumPolicy;
use App\Policies\StandingPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use App\Translation\DatabaseTranslationLoader;
use App\Support\PublicLocale;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PublicLocale::class);
        $this->app->extend('translation.loader', fn ($loader) => new DatabaseTranslationLoader($loader));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user) {
            return $user->hasRole('super-admin') ? true : null;
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(InterfaceTranslation::class, InterfaceTranslationPolicy::class);
        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(NewsCategory::class, NewsCategoryPolicy::class);
        Gate::policy(News::class, NewsPolicy::class);
        Gate::policy(City::class, CityPolicy::class);
        Gate::policy(Stadium::class, StadiumPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Player::class, PlayerPolicy::class);
        Gate::policy(Partner::class, PartnerPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
        Gate::policy(MatchFixture::class, MatchFixturePolicy::class);
        Gate::policy(MatchEvent::class, MatchEventPolicy::class);
        Gate::policy(MatchStatistic::class, MatchStatisticPolicy::class);
        Gate::policy(MatchLineup::class, MatchLineupPolicy::class);
        Gate::policy(MediaFile::class, MediaFilePolicy::class);
        Gate::policy(Standing::class, StandingPolicy::class);

        Paginator::defaultView('pagination.admin');
        Paginator::defaultSimpleView('pagination.admin-simple');

        View::composer('public.*', function ($view): void {
            $publicLocale = app(PublicLocale::class);

            $view->with('publicLanguages', $publicLocale->availableLanguages());
            $view->with('publicCurrentLanguage', $publicLocale->currentLanguage());
        });
    }
}
