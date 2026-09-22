<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Stadiums\StoreStadiumRequest;
use App\Http\Requests\Admin\Stadiums\UpdateStadiumRequest;
use App\Models\City;
use App\Models\Stadium;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StadiumController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Stadium::class, 'stadium');
    }

    public function index(Request $request): View
    {
        $stadiums = Stadium::query()
            ->with('city')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stadiums.index', [
            'stadiums' => $stadiums,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.stadiums.create', [
            'cities' => City::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreStadiumRequest $request): RedirectResponse
    {
        $stadium = Stadium::query()->create($request->validated());

        $this->recordAudit($request, $stadium, 'stadiums.created', null, $stadium->toArray());

        return redirect()->route('admin.stadiums.index')
            ->with('success', 'Stadium created successfully.');
    }

    public function edit(Stadium $stadium): View
    {
        return view('admin.stadiums.edit', [
            'stadium' => $stadium,
            'cities' => City::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateStadiumRequest $request, Stadium $stadium): RedirectResponse
    {
        $original = $stadium->toArray();
        $stadium->update($request->validated());

        $this->recordAudit($request, $stadium, 'stadiums.updated', $original, $stadium->fresh()->toArray());

        return redirect()->route('admin.stadiums.index')
            ->with('success', 'Stadium updated successfully.');
    }

    public function destroy(Request $request, Stadium $stadium): RedirectResponse
    {
        $original = $stadium->toArray();
        $stadium->delete();

        $this->recordAudit($request, $stadium, 'stadiums.deleted', $original);

        return redirect()->route('admin.stadiums.index')
            ->with('success', 'Stadium archived successfully.');
    }
}
