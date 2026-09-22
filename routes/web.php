<?php

require __DIR__.'/public.php';
require __DIR__.'/admin.php';

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('football-data-import', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'index'])
            ->name('football-data-import.index');
        Route::post('football-data-import/preview', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'preview'])
            ->name('football-data-import.preview');
        Route::post('football-data-import/preview-squads', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'previewSquads'])
            ->name('football-data-import.preview-squads');
        Route::post('football-data-import/import-squads', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'importSquads'])
            ->name('football-data-import.import-squads');
        Route::post('football-data-import/run', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'run'])
            ->name('football-data-import.run');
        Route::post('football-data-import/reconcile', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'reconcile'])
            ->name('football-data-import.reconcile');
        Route::post('football-data-import/reconcile-teams', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'reconcileTeams'])
            ->name('football-data-import.reconcile-teams');
        Route::post('football-data-import/reconcile-structure', [\App\Http\Controllers\Admin\FootballDataImportController::class, 'reconcileStructure'])
            ->name('football-data-import.reconcile-structure');
    });

Route::middleware(['public.locale', 'track.analytics'])->group(function () {
    Route::post('/newsletter/subscribe', fn () => back()->with(
        'newsletter_status',
        'Newsletter signup is disabled in this publication build.'
    ))->middleware('throttle:5,1')->name('newsletter.subscribe');
});
