<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Settings\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Setting::class, 'setting');
    }

    public function index(Request $request): View
    {
        $settings = Setting::query()
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('group_name', 'like', "%{$search}%")
                        ->orWhere('setting_key', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->string('group')->toString(), fn ($query, string $group) => $query->where('group_name', $group))
            ->orderBy('group_name')
            ->orderBy('setting_key')
            ->paginate(20)
            ->withQueryString();

        return view('admin.settings.index', [
            'settings' => $settings,
            'groups' => Setting::query()->select('group_name')->distinct()->orderBy('group_name')->pluck('group_name'),
            'filters' => $request->only(['search', 'group']),
        ]);
    }

    public function edit(Setting $setting): View
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(UpdateSettingRequest $request, Setting $setting): RedirectResponse
    {
        $original = $this->auditPayload($setting);
        $validated = $request->validated();

        $setting->update([
            'value' => $this->normalizedValue($setting, $validated['value'] ?? null),
            'is_public' => $request->boolean('is_public'),
            'autoload' => $request->boolean('autoload'),
            'description' => $validated['description'] ?? null,
        ]);

        $this->recordAudit($request, $setting, 'settings.updated', $original, $this->auditPayload($setting->fresh()));

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    private function normalizedValue(Setting $setting, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($setting->type) {
            'boolean', 'bool' => in_array($value, ['1', 'true', true, 1], true) ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function auditPayload(Setting $setting): array
    {
        return [
            'group_name' => $setting->group_name,
            'setting_key' => $setting->setting_key,
            'value' => $setting->isSensitive() ? '[protected]' : $setting->value,
            'type' => $setting->type,
            'is_public' => $setting->is_public,
            'autoload' => $setting->autoload,
            'description' => $setting->description,
        ];
    }
}
