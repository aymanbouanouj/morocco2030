<?php

namespace App\Http\Requests\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('user_type') && $this->input('user_type') === 'public') {
            return;
        }

        if (! $this->has('user_type')) {
            $this->merge([
                'user_type' => 'staff',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')],
            'user_type' => ['required', Rule::in(['staff'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'preferred_locale' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('user_type') === 'public') {
                $validator->errors()->add(
                    'user_type',
                    'Public audience accounts cannot be created from the admin panel.'
                );
            }

            $this->rejectPublicUserRoleSlugs($validator);
        });
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
            $validator->errors()->add('role_ids', 'The public-user role cannot be assigned to staff accounts.');
        }
    }
}
