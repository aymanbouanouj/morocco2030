<?php

namespace App\Http\Controllers\Site\Auth;

use App\Http\Controllers\Site\SiteController;
use App\Http\Requests\Site\Auth\RegisterRequest;
use App\Models\User;
use App\Support\AuditLogger;
use App\Support\PublicLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicRegisteredUserController extends SiteController
{
    public function create(): View
    {
        return view('public.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'preferred_locale' => $data['preferred_locale'] ?? app()->getLocale(),
            'user_type' => 'public',
            'status' => 'active',
            'password' => $data['password'],
            'last_login_at' => now(),
        ]);

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->put(PublicLocale::SESSION_KEY, $user->preferred_locale);

        AuditLogger::log($request, $user, 'public-auth.register', null, $user->fresh()->toArray());

        return redirect()->route('account.index')
            ->with('success', __('Your account is ready.'));
    }
}
