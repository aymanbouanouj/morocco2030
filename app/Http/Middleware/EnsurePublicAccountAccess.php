<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePublicAccountAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->canAccessAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        abort_unless($user->canAccessPublicAccount(), 403);

        return $next($request);
    }
}
