<?php

namespace App\Http\Middleware;

use App\Support\PublicLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function __construct(
        protected PublicLocale $publicLocale,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->publicLocale->apply($request->session()->get(PublicLocale::SESSION_KEY));

        return $next($request);
    }
}
