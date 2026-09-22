<?php

use App\Http\Middleware\EnsureAdminAccess;
use App\Http\Middleware\EnsurePublicAccountAccess;
use App\Http\Middleware\SetPublicLocale;
use App\Http\Middleware\TrackVisitorAnalytics;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            return route('login');
        });
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if (! $user) {
                return route('home');
            }

            return $user->canAccessAdmin()
                ? route('admin.dashboard')
                : ($user->canAccessPublicAccount() ? route('account.index') : route('home'));
        });

        $middleware->alias([
            'admin' => EnsureAdminAccess::class,
            'public.account' => EnsurePublicAccountAccess::class,
            'public.locale' => SetPublicLocale::class,
            'track.analytics' => TrackVisitorAnalytics::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
