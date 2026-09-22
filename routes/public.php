<?php

use App\Http\Controllers\Site\CityController;
use App\Http\Controllers\Site\AccountController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\KnockoutController;
use App\Http\Controllers\Site\LegalPageController;
use App\Http\Controllers\Site\LanguageController;
use App\Http\Controllers\Site\MapController;
use App\Http\Controllers\Site\MatchController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\PartnerController;
use App\Http\Controllers\Site\PlayerController;
use App\Http\Controllers\Site\SearchController;
use App\Http\Controllers\Site\StadiumController;
use App\Http\Controllers\Site\StandingsController;
use App\Http\Controllers\Site\TeamController;
use App\Http\Controllers\Site\Auth\NewPasswordController;
use App\Http\Controllers\Site\Auth\PasswordResetLinkController;
use App\Http\Controllers\Site\Auth\PublicAuthenticatedSessionController;
use App\Http\Controllers\Site\Auth\PublicRegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['public.locale', 'track.analytics'])->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/register', [PublicRegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [PublicRegisteredUserController::class, 'store'])->name('register.store');
        Route::get('/login', [PublicAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [PublicAuthenticatedSessionController::class, 'store'])->name('login.store');
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [PublicAuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::middleware('public.account')->group(function () {
            Route::get('/account', [AccountController::class, 'index'])->name('account.index');
            Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
            Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
            Route::get('/account/settings', [AccountController::class, 'settings'])->name('account.settings');
            Route::put('/account/settings', [AccountController::class, 'updateSettings'])->name('account.settings.update');
            Route::get('/account/favorites', [AccountController::class, 'favorites'])->name('account.favorites');
            Route::get('/account/notifications', [AccountController::class, 'notifications'])->name('account.notifications');
        });
    });

    Route::get('/search', SearchController::class)->name('search.index');
    Route::get('/map', MapController::class)->name('map.index');
    Route::get('/language/{language:code}', LanguageController::class)->name('language.switch');
    Route::get('/privacy', [LegalPageController::class, 'privacy'])->name('public.privacy');
    Route::get('/terms', [LegalPageController::class, 'terms'])->name('public.terms');

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/{slug}', [MatchController::class, 'show'])->name('matches.show');
    Route::get('/results', [MatchController::class, 'results'])->name('results.index');

    Route::get('/standings', [StandingsController::class, 'index'])->name('standings.index');
    Route::get('/standings/group/{code}', [StandingsController::class, 'show'])->name('standings.show');

    Route::get('/knockout', KnockoutController::class)->name('knockout.index');

    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{slug}', [TeamController::class, 'show'])->name('teams.show');

    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('/players/{slug}', [PlayerController::class, 'show'])->name('players.show');

    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('/cities/{slug}', [CityController::class, 'show'])->name('cities.show');

    Route::get('/stadiums', [StadiumController::class, 'index'])->name('stadiums.index');
    Route::get('/stadiums/{slug}', [StadiumController::class, 'show'])->name('stadiums.show');

    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
});
