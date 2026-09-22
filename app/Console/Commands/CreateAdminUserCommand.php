<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUserCommand extends Command
{
    protected $signature = 'admin:create-user {--role=super-admin : Role slug to assign to the new staff user}';

    protected $description = 'Create the initial staff admin user safely and assign an administrative role.';

    public function handle(): int
    {
        $roleSlug = (string) $this->option('role');
        $role = Role::query()->where('slug', $roleSlug)->first();

        if (! $role) {
            $this->error("Role [{$roleSlug}] was not found. Seed roles first with php artisan db:seed.");

            return self::FAILURE;
        }

        $data = [
            'name' => $this->ask('Full name'),
            'email' => $this->ask('Email address'),
            'password' => $this->secret('Password (minimum 8 characters)'),
            'password_confirmation' => $this->secret('Confirm password'),
            'preferred_locale' => $this->anticipate('Preferred locale code (optional)', ['ar', 'fr', 'en', 'zgh']),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'preferred_locale' => ['nullable', 'string', 'max:10'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $existing = User::query()->where('email', $data['email'])->first();

        if ($existing && ! $this->confirm("A user with {$data['email']} already exists. Promote/update this account as staff admin?", false)) {
            $this->warn('Command cancelled.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($data, $existing, $role) {
            $user = $existing ?? new User();

            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'preferred_locale' => $data['preferred_locale'] ?: null,
                'user_type' => 'staff',
                'status' => 'active',
                'password' => Hash::make($data['password']),
            ]);

            $user->save();

            $user->roles()->syncWithoutDetaching([
                $role->id => ['assigned_by' => $user->id],
            ]);
        });

        $this->info("Staff admin user prepared successfully with role [{$role->name}].");
        $this->line('You can now sign in at /admin/login using the credentials you just entered.');

        return self::SUCCESS;
    }
}
