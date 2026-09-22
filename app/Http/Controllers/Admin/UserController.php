<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Users\StoreUserRequest;
use App\Http\Requests\Admin\Users\UpdateUserRequest;
use App\Models\Language;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request): View
    {
        $accountType = $request->string('type')->toString() === 'public' ? 'public' : 'staff';

        $users = User::query()
            ->with('roles')
            ->where('user_type', $accountType)
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'accountType' => $accountType,
            'staffCount' => User::query()->where('user_type', 'staff')->count(),
            'publicCount' => User::query()->where('user_type', 'public')->count(),
            'filters' => $request->only(['search', 'status', 'type']),
            'canCreateStaff' => $request->user()->can('create', User::class),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::query()->where('slug', '!=', 'public-user')->orderBy('name')->get(),
            'languages' => Language::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roleIds = $data['role_ids'] ?? [];
        unset($data['role_ids']);

        $data['user_type'] = 'staff';

        $user = User::query()->create($data);

        if ($request->user()->can('assignRoles', $user)) {
            $user->roles()->syncWithPivotValues($roleIds, ['assigned_by' => $request->user()->id]);
        }

        $this->recordAudit($request, $user, 'users.created', null, $user->fresh()->toArray());

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Staff account created successfully.');
    }

    public function show(User $user): View
    {
        $user->load(['roles.permissions']);

        return view('admin.users.show', [
            'user' => $user,
            'recentAuditLogs' => $user->auditLogs()->latest('occurred_at')->limit(10)->get(),
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::query()->where('slug', '!=', 'public-user')->orderBy('name')->get(),
            'languages' => Language::query()->orderBy('sort_order')->get(),
            'canAssignRoles' => $request->user()->can('assignRoles', $user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $original = $user->toArray();
        $data = $request->validated();
        $roleIds = $data['role_ids'] ?? null;
        unset($data['role_ids']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($user->isStaff()) {
            $data['user_type'] = 'staff';
        } else {
            $data['user_type'] = 'public';
        }

        $user->fill($data)->save();

        if ($user->isPublic()) {
            $user->roles()->sync([]);
        } elseif ($request->user()->can('assignRoles', $user)) {
            $user->roles()->syncWithPivotValues($roleIds ?? [], ['assigned_by' => $request->user()->id]);
        }

        $this->recordAudit($request, $user, 'users.updated', $original, $user->fresh()->toArray());

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $superAdminRole = Role::query()->where('slug', 'super-admin')->first();

        if ($user->hasRole('super-admin') && $superAdminRole && $superAdminRole->users()->count() <= 1) {
            return back()->with('error', 'At least one super admin account must remain active.');
        }

        $original = $user->toArray();
        $user->delete();

        $this->recordAudit($request, $user, 'users.deleted', $original);

        $redirectType = $user->isPublic() ? 'public' : 'staff';

        return redirect()->route('admin.users.index', ['type' => $redirectType])
            ->with('success', 'User archived successfully.');
    }
}
