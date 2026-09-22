<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Site\Auth\PublicAuthenticatedSessionController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\InterfaceTranslationController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MatchEventController;
use App\Http\Controllers\Admin\MatchFixtureController;
use App\Http\Controllers\Admin\MatchLineupController;
use App\Http\Controllers\Admin\MatchOperationsController;
use App\Http\Controllers\Admin\MatchStatisticController;
use App\Http\Controllers\Admin\MediaFileController;
use App\Http\Controllers\Admin\MediaRelationController;
use App\Http\Controllers\Admin\MediaReadinessController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsWorkflowController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StadiumController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::redirect('login', '/login')->name('login');
        Route::post('login', [PublicAuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::post('news/{news}/submit-review', [NewsWorkflowController::class, 'submitForReview'])->name('news.submit-review');
        Route::post('news/{news}/approve', [NewsWorkflowController::class, 'approve'])->name('news.approve');
        Route::post('news/{news}/reject', [NewsWorkflowController::class, 'reject'])->name('news.reject');
        Route::post('news/{news}/publish', [NewsWorkflowController::class, 'publish'])->name('news.publish');
        Route::post('news/{news}/archive', [NewsWorkflowController::class, 'archive'])->name('news.archive');

        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('audit-logs', AuditLogController::class)
            ->parameters(['audit-logs' => 'auditLog'])
            ->only(['index', 'show']);
        Route::resource('languages', LanguageController::class)->only(['index', 'edit', 'update']);
        Route::get('interface-translations/missing', [InterfaceTranslationController::class, 'missing'])
            ->name('interface-translations.missing');
        Route::resource('interface-translations', InterfaceTranslationController::class)
            ->parameters(['interface-translations' => 'interfaceTranslation'])
            ->only(['index', 'edit', 'update']);
        Route::resource('groups', GroupController::class)->except(['show']);
        Route::resource('news-categories', NewsCategoryController::class)->parameters(['news-categories' => 'newsCategory'])->except(['show']);
        Route::resource('news', NewsController::class);
        Route::resource('cities', CityController::class)->except(['show']);
        Route::resource('stadiums', StadiumController::class)->except(['show']);
        Route::resource('teams', TeamController::class)->except(['show']);
        Route::resource('players', PlayerController::class)->except(['show']);
        Route::resource('partners', PartnerController::class)->except(['show']);
        Route::post('media-files/{mediaFile}/attach', [MediaFileController::class, 'attach'])->name('media-files.attach');
        Route::patch('media-files/{mediaFile}/archive', [MediaFileController::class, 'archive'])->name('media-files.archive');
        Route::patch('media-files/{mediaFile}/restore', [MediaFileController::class, 'restore'])->name('media-files.restore');
        Route::delete('media-relations/{mediaRelation}', [MediaRelationController::class, 'destroy'])->name('media-relations.destroy');
        Route::resource('media-files', MediaFileController::class)
            ->parameters(['media-files' => 'mediaFile'])
            ->only(['index', 'show', 'create', 'store', 'edit', 'update']);
        Route::get('media-readiness', MediaReadinessController::class)->name('media-readiness.index');
        Route::resource('settings', SettingController::class)->only(['index', 'edit', 'update']);
        Route::resource('contact-messages', ContactMessageController::class)
            ->parameters(['contact-messages' => 'contactMessage'])
            ->only(['index', 'show', 'update']);
        Route::post('matches/{match}/recalculate-standings', [MatchOperationsController::class, 'recalculateStandings'])
            ->name('matches.recalculate-standings');
        Route::post('matches/{match}/propagate-knockout', [MatchOperationsController::class, 'propagateKnockout'])
            ->name('matches.propagate-knockout');
        Route::resource('matches.events', MatchEventController::class)
            ->scoped()
            ->except(['show']);
        Route::resource('matches.statistics', MatchStatisticController::class)
            ->scoped()
            ->except(['show']);
        Route::resource('matches.lineups', MatchLineupController::class)
            ->scoped()
            ->except(['show']);
        Route::resource('matches', MatchFixtureController::class)->parameters(['matches' => 'match']);
    });
});
