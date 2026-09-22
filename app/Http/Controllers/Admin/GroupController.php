<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Groups\StoreGroupRequest;
use App\Http\Requests\Admin\Groups\UpdateGroupRequest;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Group::class, 'group');
    }

    public function index(Request $request): View
    {
        $groups = Group::query()
            ->withCount(['teams', 'matches', 'standings'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.groups.index', [
            'groups' => $groups,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): View
    {
        return view('admin.groups.create');
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $group = Group::query()->create($request->validated());

        $this->recordAudit($request, $group, 'groups.created', null, $group->toArray());

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    public function edit(Group $group): View
    {
        return view('admin.groups.edit', compact('group'));
    }

    public function update(UpdateGroupRequest $request, Group $group): RedirectResponse
    {
        $original = $group->toArray();
        $group->update($request->validated());

        $this->recordAudit($request, $group, 'groups.updated', $original, $group->fresh()->toArray());

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully.');
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        if ($group->teams()->exists() || $group->matches()->exists() || $group->standings()->exists()) {
            return back()->with('error', 'This group is in use by teams, matches, or standings and cannot be deleted.');
        }

        $original = $group->toArray();
        $group->delete();

        $this->recordAudit($request, $group, 'groups.deleted', $original);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully.');
    }
}
