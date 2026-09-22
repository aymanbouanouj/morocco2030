<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DemoAccessControlSeeder extends Seeder
{
    /**
     * Demo role slugs mapped to permission slugs (existing PermissionSeeder slugs only).
     *
     * @var array<string, list<string>>
     */
    public const DEMO_ROLE_PERMISSIONS = [
        'platform-admin' => [
            'translations.manage',
            'languages.manage',
            'news.manage',
            'news.review',
            'news.publish',
            'media.manage',
            'partners.manage',
            'groups.manage',
            'cities.manage',
            'stadiums.manage',
            'teams.manage',
            'players.manage',
            'matches.manage',
            'standings.manage',
            'page-blocks.manage',
            'menus.manage',
            'notifications.manage',
            'contact-messages.manage',
            'analytics.view',
            'audit-logs.view',
            'settings.manage',
        ],
        'match-manager' => [
            'matches.manage',
            'standings.manage',
        ],
        'team-manager' => [
            'teams.manage',
            'players.manage',
            'media.manage',
        ],
        'venue-manager' => [
            'cities.manage',
            'stadiums.manage',
            'media.manage',
        ],
        'sponsor-manager' => [
            'partners.manage',
            'media.manage',
        ],
        'media-manager' => [
            'media.manage',
        ],
    ];

    /**
     * @var array<string, array{name: string, description: string, permissions: list<string>|string}>
     */
    public const DEMO_ROLE_DEFINITIONS = [
        'platform-admin' => [
            'name' => 'Platform Admin',
            'description' => 'Operational admin access without identity/RBAC management.',
            'permissions' => 'platform-admin',
        ],
        'match-manager' => [
            'name' => 'Match Manager',
            'description' => 'Manages fixtures, match operations, and standings recalculation.',
            'permissions' => 'match-manager',
        ],
        'team-manager' => [
            'name' => 'Team Manager',
            'description' => 'Manages teams, players, and related media attachments.',
            'permissions' => 'team-manager',
        ],
        'venue-manager' => [
            'name' => 'Venue Manager',
            'description' => 'Manages host cities, stadiums, and venue media.',
            'permissions' => 'venue-manager',
        ],
        'sponsor-manager' => [
            'name' => 'Sponsor Manager',
            'description' => 'Manages partners/sponsors and sponsor media assets.',
            'permissions' => 'sponsor-manager',
        ],
        'media-manager' => [
            'name' => 'Media Manager',
            'description' => 'Manages media library assets, readiness, and attachments.',
            'permissions' => 'media-manager',
        ],
    ];

    /**
     * @var array<string, array{email: string, name: string, role_slug: string|null, user_type: string}>
     */
    public const DEMO_USERS = [
        'super-admin' => [
            'email' => 'superadmin@morocco2030.test',
            'name' => 'Demo Super Admin',
            'role_slug' => 'super-admin',
            'user_type' => 'staff',
        ],
        'platform-admin' => [
            'email' => 'platform.admin@morocco2030.test',
            'name' => 'Demo Platform Admin',
            'role_slug' => 'platform-admin',
            'user_type' => 'staff',
        ],
        'chief-editor' => [
            'email' => 'chief.editor@morocco2030.test',
            'name' => 'Demo Chief Editor',
            'role_slug' => 'chief-editor',
            'user_type' => 'staff',
        ],
        'journalist' => [
            'email' => 'journalist@morocco2030.test',
            'name' => 'Demo Journalist',
            'role_slug' => 'journalist',
            'user_type' => 'staff',
        ],
        'match-manager' => [
            'email' => 'match.manager@morocco2030.test',
            'name' => 'Demo Match Manager',
            'role_slug' => 'match-manager',
            'user_type' => 'staff',
        ],
        'team-manager' => [
            'email' => 'team.manager@morocco2030.test',
            'name' => 'Demo Team Manager',
            'role_slug' => 'team-manager',
            'user_type' => 'staff',
        ],
        'venue-manager' => [
            'email' => 'venue.manager@morocco2030.test',
            'name' => 'Demo Venue Manager',
            'role_slug' => 'venue-manager',
            'user_type' => 'staff',
        ],
        'sponsor-manager' => [
            'email' => 'sponsor.manager@morocco2030.test',
            'name' => 'Demo Sponsor Manager',
            'role_slug' => 'sponsor-manager',
            'user_type' => 'staff',
        ],
        'media-manager' => [
            'email' => 'media.manager@morocco2030.test',
            'name' => 'Demo Media Manager',
            'role_slug' => 'media-manager',
            'user_type' => 'staff',
        ],
        'translator' => [
            'email' => 'translator@morocco2030.test',
            'name' => 'Demo Translator',
            'role_slug' => 'translator',
            'user_type' => 'staff',
        ],
        'analyst' => [
            'email' => 'analyst@morocco2030.test',
            'name' => 'Demo Analyst',
            'role_slug' => 'analyst',
            'user_type' => 'staff',
        ],
        'support-agent' => [
            'email' => 'support.agent@morocco2030.test',
            'name' => 'Demo Support Agent',
            'role_slug' => 'support-agent',
            'user_type' => 'staff',
        ],
        'public-user' => [
            'email' => 'public.user@morocco2030.test',
            'name' => 'Demo Public User',
            'role_slug' => null,
            'user_type' => 'public',
        ],
    ];

    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        $this->seedDemoRoles();
        $credentials = $this->seedDemoUsers();

        $this->writeCredentialsFile($credentials);

        if ($this->command !== null) {
            $this->command->warn('Demo credentials are stored only in docs/demo/CREDENTIALS.local.md, which must stay untracked.');
        }
    }

    private function seedDemoRoles(): void
    {
        foreach (self::DEMO_ROLE_DEFINITIONS as $slug => $definition) {
            $permissionSlugs = self::DEMO_ROLE_PERMISSIONS[$definition['permissions']];

            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'is_system' => true,
                ]
            );

            $permissionIds = Permission::query()
                ->whereIn('slug', $permissionSlugs)
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }

    /**
     * @return array<string, string> email => password (for local credentials file only)
     */
    private function seedDemoUsers(): array
    {
        $sharedPassword = env('MOROCCO2030_DEMO_USERS_PASSWORD');
        $credentials = [];

        foreach (self::DEMO_USERS as $definition) {
            $password = $sharedPassword ?: Str::password(24);
            $credentials[$definition['email']] = $password;

            $user = User::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'user_type' => $definition['user_type'],
                    'status' => 'active',
                    'preferred_locale' => 'en',
                    'password' => $password,
                ]
            );

            if ($definition['role_slug'] === null) {
                $user->roles()->sync([]);

                continue;
            }

            $role = Role::query()->where('slug', $definition['role_slug'])->first();

            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        return $credentials;
    }

    /**
     * @param  array<string, string>  $credentials
     */
    private function writeCredentialsFile(array $credentials): void
    {
        if (env('MOROCCO2030_DEMO_USERS_PASSWORD')) {
            return;
        }

        $directory = base_path('docs/demo');
        File::ensureDirectoryExists($directory);

        $lines = [
            '# MOROCCO 2030 — local demo credentials (DO NOT COMMIT)',
            '',
            'Generated: '.now()->toDateTimeString(),
            '',
            'These passwords were generated for local/demo use only.',
            'Set `MOROCCO2030_DEMO_USERS_PASSWORD` in your environment to use one shared password instead.',
            '',
            '| Email | Password |',
            '|-------|----------|',
        ];

        foreach ($credentials as $email => $password) {
            $lines[] = sprintf('| %s | %s |', $email, $password);
        }

        File::put($directory.'/CREDENTIALS.local.md', implode(PHP_EOL, $lines).PHP_EOL);
    }
}
