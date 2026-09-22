<?php

namespace App\Http\Controllers\Site\Auth;

use App\Http\Controllers\Site\SiteController;
use App\Http\Requests\Site\Auth\PublicLoginRequest;
use App\Support\AuditLogger;
use App\Support\PublicLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicAuthenticatedSessionController extends SiteController
{
    public function create(): View
    {
        return view('public.auth.login');
    }

    public function store(PublicLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_login_at' => now(),
        ])->saveQuietly();

        if ($request->user()->preferred_locale) {
            $request->session()->put(PublicLocale::SESSION_KEY, $request->user()->preferred_locale);
        }

        AuditLogger::log(
            $request,
            $request->user(),
            'auth.login',
            null,
            ['last_login_at' => $request->user()->last_login_at?->toDateTimeString()]
        );

        if ($request->user()->canAccessAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', __('Welcome back.'));
        }

        return redirect()->route('account.index')
            ->with('success', __('Welcome back.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            AuditLogger::log($request, $user, 'auth.logout');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', __('You have been signed out.'));
    }
}
