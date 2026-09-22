<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Languages\UpdateLanguageRequest;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Language::class, 'language');
    }

    public function index(Request $request): View
    {
        $languages = Language::query()
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('native_name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('locale', 'like', "%{$search}%");
                });
            })
            ->when($request->string('direction')->toString(), fn ($query, string $direction) => $query->where('direction', $direction))
            ->when($request->string('status')->toString() !== '', function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.languages.index', [
            'languages' => $languages,
            'filters' => $request->only(['search', 'direction', 'status']),
        ]);
    }

    public function edit(Language $language): View
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        $original = $language->toArray();

        $language->update($request->validated());

        $this->recordAudit($request, $language, 'languages.updated', $original, $language->fresh()->toArray());

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated successfully.');
    }
}
