<?php

namespace App\Http\Controllers\Site;

use Illuminate\View\View;

class LegalPageController extends SiteController
{
    public function privacy(): View
    {
        return view('public.legal.privacy');
    }

    public function terms(): View
    {
        return view('public.legal.terms');
    }
}
