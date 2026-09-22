<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\InterfaceTranslations\UpdateInterfaceTranslationRequest;
use App\Models\InterfaceTranslation;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterfaceTranslationController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(InterfaceTranslation::class, 'interfaceTranslation');
    }

    public function index(Request $request): View
    {
        $translations = InterfaceTranslation::query()
            ->with('language')
            ->when($request->integer('language_id'), fn ($query, int $languageId) => $query->where('language_id', $languageId))
            ->when($request->string('namespace')->toString(), fn ($query, string $namespace) => $query->where('namespace', $namespace))
            ->when($request->string('group_name')->toString(), fn ($query, string $group) => $query->where('group_name', $group))
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('translation_key', 'like', "%{$search}%")
                        ->orWhere('value', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString() === 'empty', fn ($query) => $query->where('value', ''))
            ->join('languages', 'interface_translations.language_id', '=', 'languages.id')
            ->orderBy('languages.sort_order')
            ->orderBy('interface_translations.namespace')
            ->orderBy('interface_translations.group_name')
            ->orderBy('interface_translations.translation_key')
            ->select('interface_translations.*')
            ->paginate(25)
            ->withQueryString();

        return view('admin.interface-translations.index', [
            'translations' => $translations,
            'languages' => Language::query()->orderBy('sort_order')->orderBy('name')->get(),
            'namespaces' => InterfaceTranslation::query()->select('namespace')->distinct()->orderBy('namespace')->pluck('namespace'),
            'groups' => InterfaceTranslation::query()->select('group_name')->distinct()->orderBy('group_name')->pluck('group_name'),
            'filters' => $request->only(['language_id', 'namespace', 'group_name', 'search', 'status']),
        ]);
    }

    public function missing(): View
    {
        $this->authorize('viewAny', InterfaceTranslation::class);

        $languages = Language::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $keys = InterfaceTranslation::query()
            ->select('namespace', 'group_name', 'translation_key')
            ->distinct()
            ->orderBy('namespace')
            ->orderBy('group_name')
            ->orderBy('translation_key')
            ->get();

        $translations = InterfaceTranslation::query()
            ->get(['language_id', 'namespace', 'group_name', 'translation_key', 'value'])
            ->keyBy(fn (InterfaceTranslation $translation) => $this->translationLookupKey(
                (int) $translation->language_id,
                $translation->namespace,
                $translation->group_name,
                $translation->translation_key
            ));

        $rows = $languages->map(function (Language $language) use ($keys, $translations) {
            $missing = collect();
            $empty = 0;

            foreach ($keys as $key) {
                $lookupKey = $this->translationLookupKey(
                    (int) $language->id,
                    $key->namespace,
                    $key->group_name,
                    $key->translation_key
                );

                $translation = $translations->get($lookupKey);

                if (! $translation) {
                    $missing->push($key);

                    continue;
                }

                if (trim((string) $translation->value) === '') {
                    $empty++;
                }
            }

            return [
                'language' => $language,
                'total_keys' => $keys->count(),
                'missing_count' => $missing->count(),
                'empty_count' => $empty,
                'complete_count' => max(0, $keys->count() - $missing->count() - $empty),
                'sample_missing' => $missing->take(8)->values(),
            ];
        });

        return view('admin.interface-translations.missing', [
            'rows' => $rows,
            'totalKeys' => $keys->count(),
        ]);
    }

    public function edit(InterfaceTranslation $interfaceTranslation): View
    {
        $interfaceTranslation->load('language');

        return view('admin.interface-translations.edit', compact('interfaceTranslation'));
    }

    public function update(UpdateInterfaceTranslationRequest $request, InterfaceTranslation $interfaceTranslation): RedirectResponse
    {
        $original = $interfaceTranslation->toArray();
        $validated = $request->validated();

        $interfaceTranslation->update([
            'value' => (string) ($validated['value'] ?? ''),
        ]);

        $this->recordAudit(
            $request,
            $interfaceTranslation,
            'interface-translations.updated',
            $original,
            $interfaceTranslation->fresh()->toArray()
        );

        return redirect()->route('admin.interface-translations.index', [
            'language_id' => $interfaceTranslation->language_id,
            'namespace' => $interfaceTranslation->namespace,
            'group_name' => $interfaceTranslation->group_name,
        ])->with('success', 'Interface translation updated successfully.');
    }

    private function translationLookupKey(int $languageId, string $namespace, string $group, string $key): string
    {
        return implode('|', [$languageId, $namespace, $group, $key]);
    }
}
