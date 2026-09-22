<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'identity', 'description' => 'Review platform user accounts.'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'module' => 'identity', 'description' => 'Create, update, or deactivate platform users.'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'identity', 'description' => 'Manage role definitions and assignments.'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'identity', 'description' => 'Manage permission mappings.'],
            ['name' => 'Manage Languages', 'slug' => 'languages.manage', 'module' => 'localization', 'description' => 'Configure active languages and defaults.'],
            ['name' => 'Manage Translations', 'slug' => 'translations.manage', 'module' => 'localization', 'description' => 'Edit content and interface translations.'],
            ['name' => 'Manage News', 'slug' => 'news.manage', 'module' => 'editorial', 'description' => 'Create and edit editorial news content.'],
            ['name' => 'Review News', 'slug' => 'news.review', 'module' => 'editorial', 'description' => 'Review and approve editorial news submissions.'],
            ['name' => 'Publish News', 'slug' => 'news.publish', 'module' => 'editorial', 'description' => 'Approve and publish editorial news content.'],
            ['name' => 'Manage Media', 'slug' => 'media.manage', 'module' => 'media', 'description' => 'Upload and organize media assets.'],
            ['name' => 'Manage Partners', 'slug' => 'partners.manage', 'module' => 'platform', 'description' => 'Manage sponsors and partners.'],
            ['name' => 'Manage Groups', 'slug' => 'groups.manage', 'module' => 'competition', 'description' => 'Manage competition groups and their ordering.'],
            ['name' => 'Manage Cities', 'slug' => 'cities.manage', 'module' => 'competition', 'description' => 'Manage host cities.'],
            ['name' => 'Manage Stadiums', 'slug' => 'stadiums.manage', 'module' => 'competition', 'description' => 'Manage stadium and venue records.'],
            ['name' => 'Manage Teams', 'slug' => 'teams.manage', 'module' => 'competition', 'description' => 'Manage teams and squads.'],
            ['name' => 'Manage Players', 'slug' => 'players.manage', 'module' => 'competition', 'description' => 'Manage player records.'],
            ['name' => 'Manage Matches', 'slug' => 'matches.manage', 'module' => 'competition', 'description' => 'Manage fixtures, scheduling, and results.'],
            ['name' => 'Manage Standings', 'slug' => 'standings.manage', 'module' => 'competition', 'description' => 'Manage group standings and bracket progressions.'],
            ['name' => 'Manage Page Blocks', 'slug' => 'page-blocks.manage', 'module' => 'content', 'description' => 'Manage dynamic page block content.'],
            ['name' => 'Manage Menus', 'slug' => 'menus.manage', 'module' => 'content', 'description' => 'Manage site navigation menus.'],
            ['name' => 'Manage Notifications', 'slug' => 'notifications.manage', 'module' => 'engagement', 'description' => 'Manage user-facing platform notifications.'],
            ['name' => 'Manage Contact Messages', 'slug' => 'contact-messages.manage', 'module' => 'engagement', 'description' => 'Review and respond to contact messages.'],
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'module' => 'analytics', 'description' => 'View visitor and sports analytics.'],
            ['name' => 'View Audit Logs', 'slug' => 'audit-logs.view', 'module' => 'analytics', 'description' => 'Review audit trail entries.'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'platform', 'description' => 'Manage platform configuration settings.'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
