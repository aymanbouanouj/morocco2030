<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Cities\StoreCityRequest;
use App\Http\Requests\Admin\Cities\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(City::class, 'city');
    }

    public function index(Request $request): View
    {
        $cities = City::query()
            ->withCount(['stadiums'])
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

        return view('admin.cities.index', [
            'cities' => $cities,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.cities.create');
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        $city = City::query()->create($request->validated());

        $this->recordAudit($request, $city, 'cities.created', null, $city->toArray());

        return redirect()->route('admin.cities.index')
            ->with('success', 'City created successfully.');
    }

    public function edit(City $city): View
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $original = $city->toArray();
        $city->update($request->validated());

        $this->recordAudit($request, $city, 'cities.updated', $original, $city->fresh()->toArray());

        return redirect()->route('admin.cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function destroy(Request $request, City $city): RedirectResponse
    {
        $original = $city->toArray();
        $city->delete();

        $this->recordAudit($request, $city, 'cities.deleted', $original);

        return redirect()->route('admin.cities.index')
            ->with('success', 'City archived successfully.');
    }
}
