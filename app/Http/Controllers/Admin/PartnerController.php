<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Partners\StorePartnerRequest;
use App\Http\Requests\Admin\Partners\UpdatePartnerRequest;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PartnerController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Partner::class, 'partner');
    }

    public function index(Request $request): View
    {
        $partners = Partner::query()
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.partners.index', [
            'partners' => $partners,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.partners.create');
    }

    public function store(StorePartnerRequest $request): RedirectResponse
    {
        $data = $this->partnerDataWithLogo($request, $request->validated());
        $partner = Partner::query()->create($data);

        $this->recordAudit($request, $partner, 'partners.created', null, $partner->toArray());

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    public function edit(Partner $partner): View
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(UpdatePartnerRequest $request, Partner $partner): RedirectResponse
    {
        $original = $partner->toArray();
        $data = $this->partnerDataWithLogo($request, $request->validated(), $partner);
        $partner->update($data);

        $this->recordAudit($request, $partner, 'partners.updated', $original, $partner->fresh()->toArray());

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    public function destroy(Request $request, Partner $partner): RedirectResponse
    {
        $original = $partner->toArray();
        $partner->delete();

        $this->recordAudit($request, $partner, 'partners.deleted', $original);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner archived successfully.');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function partnerDataWithLogo(Request $request, array $data, ?Partner $partner = null): array
    {
        $logoData = $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_alt' => ['nullable', 'string', 'max:255'],
        ]);

        $data['logo_alt'] = $logoData['logo_alt'] ?? null;

        if ($request->hasFile('logo')) {
            $this->deleteStoredLogo($partner?->logo_path);
            $data['logo_path'] = $request->file('logo')->store('partners/logos', 'public');
        }

        return $data;
    }

    private function deleteStoredLogo(?string $path): void
    {
        if (! is_string($path) || ! str_starts_with($path, 'partners/logos/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
