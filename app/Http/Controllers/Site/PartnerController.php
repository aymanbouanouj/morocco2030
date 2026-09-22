<?php

namespace App\Http\Controllers\Site;

use App\Models\Partner;
use Illuminate\View\View;

class PartnerController extends SiteController
{
    public function index(): View
    {
        $partners = Partner::query()
            ->publiclyVisible()
            ->with(['mediaRelations.mediaFile', 'translations.language'])
            ->publicOrder()
            ->paginate(18);

        return view('public.partners.index', [
            'partners' => $partners,
        ]);
    }
}
