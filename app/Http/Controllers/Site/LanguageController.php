<?php

namespace App\Http\Controllers\Site;

use App\Models\Language;
use App\Support\PublicLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends SiteController
{
    public function __invoke(Request $request, Language $language): RedirectResponse
    {
        abort_unless($language->is_active, 404);

        $request->session()->put(PublicLocale::SESSION_KEY, $language->code);

        $previous = url()->previous();
        $fallback = route('home');

        if (! filled($previous) || $previous === $request->fullUrl()) {
            return redirect()->to($fallback);
        }

        $parts = parse_url($previous);

        if ($parts === false || ! isset($parts['path']) || ! str_starts_with($parts['path'], '/')) {
            return redirect()->to($fallback);
        }

        if (isset($parts['scheme']) && ! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return redirect()->to($fallback);
        }

        if (isset($parts['host']) && ! hash_equals(strtolower($request->getHost()), strtolower($parts['host']))) {
            return redirect()->to($fallback);
        }

        if (isset($parts['port']) && (int) $parts['port'] !== $request->getPort()) {
            return redirect()->to($fallback);
        }

        $target = $parts['path'];

        if (isset($parts['query']) && $parts['query'] !== '') {
            $target .= '?'.$parts['query'];
        }

        return redirect()->to($target);
    }
}
