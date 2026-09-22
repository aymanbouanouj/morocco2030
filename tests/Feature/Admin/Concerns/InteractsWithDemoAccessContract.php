<?php

namespace Tests\Feature\Admin\Concerns;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

trait InteractsWithDemoAccessContract
{
    protected function seedDemoAccessFoundation(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }

    protected function demoUser(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }

    /**
     * @return list<string>
     */
    protected function extractSidebarHrefs(string $html): array
    {
        if (preg_match('/<aside id="admin-sidebar"[^>]*>(.*)<\/aside>/s', $html, $matches) !== 1) {
            $this->fail('Admin sidebar markup was not found in the dashboard response.');
        }

        preg_match_all('/href="([^"]+)"/', $matches[1], $hrefMatches);

        return $hrefMatches[1] ?? [];
    }

    protected function routeExists(string $routeName): bool
    {
        return \Illuminate\Support\Facades\Route::has($routeName);
    }
}
