<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full administrative access to all platform capabilities.',
                'is_system' => true,
                'permissions' => ['*'],
            ],
            [
                'name' => 'Content Editor',
                'slug' => 'content-editor',
                'description' => 'Manages multilingual editorial and page content.',
                'is_system' => true,
                'permissions' => [
                    'languages.manage',
                    'translations.manage',
                    'news.manage',
                    'news.review',
                    'news.publish',
                    'media.manage',
                    'page-blocks.manage',
                    'menus.manage',
                    'partners.manage',
                ],
            ],
            [
                'name' => 'Competition Manager',
                'slug' => 'competition-manager',
                'description' => 'Manages tournament structures, teams, players, and fixtures.',
                'is_system' => true,
                'permissions' => [
                    'groups.manage',
                    'cities.manage',
                    'stadiums.manage',
                    'teams.manage',
                    'players.manage',
                    'matches.manage',
                    'standings.manage',
                    'media.manage',
                ],
            ],
            [
                'name' => 'Translator',
                'slug' => 'translator',
                'description' => 'Maintains platform and content translations.',
                'is_system' => true,
                'permissions' => [
                    'languages.manage',
                    'translations.manage',
                ],
            ],
            [
                'name' => 'Journalist',
                'slug' => 'journalist',
                'description' => 'Creates and manages own editorial drafts.',
                'is_system' => true,
                'permissions' => [
                    'news.manage',
                    'media.manage',
                ],
            ],
            [
                'name' => 'Chief Editor',
                'slug' => 'chief-editor',
                'description' => 'Reviews, approves, and publishes editorial content.',
                'is_system' => true,
                'permissions' => [
                    'news.manage',
                    'news.review',
                    'news.publish',
                    'media.manage',
                ],
            ],
            [
                'name' => 'Analyst',
                'slug' => 'analyst',
                'description' => 'Accesses analytics and audit data for reporting.',
                'is_system' => true,
                'permissions' => [
                    'analytics.view',
                    'audit-logs.view',
                ],
            ],
            [
                'name' => 'Support Agent',
                'slug' => 'support-agent',
                'description' => 'Handles incoming contact messages and user notices.',
                'is_system' => true,
                'permissions' => [
                    'notifications.manage',
                    'contact-messages.manage',
                ],
            ],
        ];

        $allPermissionIds = Permission::query()->pluck('id');

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            $permissionIds = $permissions === ['*']
                ? $allPermissionIds
                : Permission::query()->whereIn('slug', $permissions)->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
