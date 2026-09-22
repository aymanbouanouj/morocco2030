<?php

namespace App\Http\Controllers\Site\Auth;

use App\Http\Controllers\Site\SiteController;
use App\Http\Requests\Site\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends SiteController
{
    public function create(): View
    {
        return view('public.auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->first();

        if ($user?->canAccessPublicAccount()) {
            Password::broker()->sendResetLink([
                'email' => $user->email,
            ]);
        }

        return back()->with('status', __('If a matching public account exists, a reset link has been sent to that email address.'));
    }
}
