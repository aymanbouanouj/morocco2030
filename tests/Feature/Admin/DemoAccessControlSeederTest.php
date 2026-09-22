<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoAccessControlSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_users_roles_and_is_idempotent(): void
    {
        $this->seedDemoAccessLayer();
        $this->seedDemoAccessLayer();

        $emails = collect(DemoAccessControlSeeder::DEMO_USERS)->pluck('email');

        $this->assertSame($emails->count(), User::query()->whereIn('email', $emails)->count());

        foreach (DemoAccessControlSeeder::DEMO_USERS as $definition) {
            $user = User::query()->where('email', $definition['email'])->first();

            $this->assertNotNull($user);
            $this->assertSame($definition['user_type'], $user->user_type);

            if ($definition['role_slug'] === null) {
                $this->assertCount(0, $user->roles);

                continue;
            }

            $this->assertTrue($user->hasRole($definition['role_slug']));
        }

        foreach (array_keys(DemoAccessControlSeeder::DEMO_ROLE_DEFINITIONS) as $slug) {
            $this->assertNotNull(Role::query()->where('slug', $slug)->first());
        }
    }

    public function test_demo_staff_users_share_configured_password(): void
    {
        $this->seedDemoAccessLayer();

        $password = (string) env('MOROCCO2030_DEMO_USERS_PASSWORD', 'Morocco2030-Local-Demo-Only');

        $staff = User::query()
            ->where('user_type', 'staff')
            ->whereIn('email', collect(DemoAccessControlSeeder::DEMO_USERS)->pluck('email'))
            ->get();

        $this->assertNotEmpty($staff);

        foreach ($staff as $user) {
            $this->assertTrue(
                auth()->getProvider()->validateCredentials($user, ['password' => $password]),
                "Password validation failed for {$user->email}"
            );
        }
    }

    private function seedDemoAccessLayer(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }
}
