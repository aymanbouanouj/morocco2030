<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Roles\StoreRoleRequest;
use App\Http\Requests\Admin\Roles\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(Request $request): View
    {
        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.roles.index', [
            'roles' => $roles,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissionsByModule' => Permission::query()->orderBy('module')->orderBy('name')->get()->groupBy('module'),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);
        $data['is_system'] = $request->boolean('is_system');

        $role = Role::query()->create($data);
        $role->permissions()->sync($permissionIds);

        $this->recordAudit($request, $role, 'roles.created', null, $role->fresh('permissions')->toArray());

        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role): View
    {
        $role->load(['permissions', 'users']);

        return view('admin.roles.show', [
            'role' => $role,
        ]);
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionsByModule' => Permission::query()->orderBy('module')->orderBy('name')->get()->groupBy('module'),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $original = $role->load('permissions')->toArray();
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);
        $data['is_system'] = $request->boolean('is_system');

        $role->fill($data)->save();
        $role->permissions()->sync($permissionIds);

        $this->recordAudit($request, $role, 'roles.updated', $original, $role->fresh('permissions')->toArray());

        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Remove assigned users before deleting this role.');
        }

        $original = $role->toArray();
        $role->delete();

        $this->recordAudit($request, $role, 'roles.deleted', $original);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
