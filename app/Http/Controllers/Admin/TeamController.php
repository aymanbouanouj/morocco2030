<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Teams\StoreTeamRequest;
use App\Http\Requests\Admin\Teams\UpdateTeamRequest;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Team::class, 'team');
    }

    public function index(Request $request): View
    {
        $teams = Team::query()
            ->with('group')
            ->withCount('players')
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(48)
            ->withQueryString();

        return view('admin.teams.index', [
            'teams' => $teams,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.teams.create', [
            'groups' => Group::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTeamRequest $request): RedirectResponse
    {
        $team = Team::query()->create($request->validated());

        $this->recordAudit($request, $team, 'teams.created', null, $team->toArray());

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function edit(Team $team): View
    {
        return view('admin.teams.edit', [
            'team' => $team,
            'groups' => Group::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $original = $team->toArray();
        $team->update($request->validated());

        $this->recordAudit($request, $team, 'teams.updated', $original, $team->fresh()->toArray());

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Request $request, Team $team): RedirectResponse
    {
        $original = $team->toArray();
        $team->delete();

        $this->recordAudit($request, $team, 'teams.deleted', $original);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team archived successfully.');
    }
}
