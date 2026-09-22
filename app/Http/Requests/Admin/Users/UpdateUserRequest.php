<?php

namespace App\Http\Requests\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        $target = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($userId)],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'preferred_locale' => ['nullable', 'string', 'max:10'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'user_type' => ['prohibited'],
            'role_ids' => ['prohibited'],
        ];

        if ($target->isStaff() && $this->user()->can('assignRoles', $target)) {
            $rules['role_ids'] = ['nullable', 'array'];
            $rules['role_ids.*'] = ['integer', 'exists:roles,id'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $target = $this->route('user');

        if ($target->isPublic()) {
            return;
        }

        if (! $this->user()->can('assignRoles', $target)) {
            $this->merge([
                'role_ids' => $target->roles->pluck('id')->all(),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $target = $this->route('user');

            if ($target->isPublic() && $this->filled('role_ids')) {
                $validator->errors()->add('role_ids', 'Public audience accounts cannot be assigned admin roles.');
            }

            if ($target->isPublic() && $this->input('user_type') === 'staff') {
                $validator->errors()->add('user_type', 'Public audience accounts cannot be converted to staff.');
            }

            if ($target->isStaff() && $this->input('user_type') === 'public') {
                $validator->errors()->add('user_type', 'Staff accounts cannot be converted to public audience.');
            }

            $this->rejectPublicUserRoleSlugs($validator);
        });
    }

    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if ($key !== null) {
            return $data;
        }

        $target = $this->route('user');
        $data['user_type'] = $target->isStaff() ? 'staff' : 'public';

        if ($target->isPublic()) {
            unset($data['role_ids']);
        }

        return $data;
    }

    private function rejectPublicUserRoleSlugs(Validator $validator): void
    {
        $roleIds = $this->input('role_ids', []);

        if ($roleIds === []) {
            return;
        }

        $forbidden = Role::query()
            ->whereIn('id', $roleIds)
            ->whereIn('slug', ['public-user'])
            ->exists();

        if ($forbidden) {
            $validator->errors()->add('role_ids', 'The public-user role cannot be assigned.');
        }
    }
}
