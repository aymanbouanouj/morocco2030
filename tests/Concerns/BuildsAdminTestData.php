<?php

namespace Tests\Concerns;

use App\Models\City;
use App\Models\Group;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Permission;
use App\Models\Player;
use App\Models\Role;
use App\Models\Stadium;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

trait BuildsAdminTestData
{
    protected function makeStaffUser(array $permissionSlugs = [], array $attributes = []): User
    {
        return $this->makeUserWithRole('staff', $permissionSlugs, $attributes);
    }

    protected function makePublicUser(array $permissionSlugs = [], array $attributes = []): User
    {
        return $this->makeUserWithRole('public', $permissionSlugs, $attributes);
    }

    protected function makeGroup(string $code = 'A', array $attributes = []): Group
    {
        return Group::query()->create([
            'name' => $attributes['name'] ?? 'Group '.$code,
            'code' => $attributes['code'] ?? $code,
            'description' => $attributes['description'] ?? null,
            'sort_order' => $attributes['sort_order'] ?? (Group::query()->count() + 1),
        ]);
    }

    protected function makeCity(string $name = 'Casablanca', array $attributes = []): City
    {
        $slug = Str::slug($attributes['slug'] ?? $name);
        $sequence = City::query()->count() + 1;

        return City::query()->create([
            'name' => $name,
            'slug' => $slug.'-'.$sequence,
            'code' => $attributes['code'] ?? 'CITY'.$sequence,
            'country_code' => $attributes['country_code'] ?? 'MA',
            'region' => $attributes['region'] ?? 'Test Region',
            'latitude' => $attributes['latitude'] ?? null,
            'longitude' => $attributes['longitude'] ?? null,
            'description' => $attributes['description'] ?? null,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    protected function makeStadium(City $city, string $name = 'Test Stadium', array $attributes = []): Stadium
    {
        $slug = Str::slug($attributes['slug'] ?? $name);
        $sequence = Stadium::query()->count() + 1;

        return Stadium::query()->create([
            'city_id' => $attributes['city_id'] ?? $city->id,
            'name' => $name,
            'slug' => $slug.'-'.$sequence,
            'code' => $attributes['code'] ?? 'STD'.$sequence,
            'capacity' => $attributes['capacity'] ?? 60000,
            'address' => $attributes['address'] ?? 'Test venue',
            'latitude' => $attributes['latitude'] ?? null,
            'longitude' => $attributes['longitude'] ?? null,
            'surface_type' => $attributes['surface_type'] ?? 'grass',
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    protected function makeTeam(Group $group, string $name = 'Test Team', array $attributes = []): Team
    {
        $slug = Str::slug($attributes['slug'] ?? $name);
        $sequence = Team::query()->count() + 1;
        $baseCode = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', Str::upper($slug)) ?: 'TM', 0, 3));

        return Team::query()->create([
            'group_id' => $attributes['group_id'] ?? $group->id,
            'name' => $name,
            'short_name' => $attributes['short_name'] ?? Str::upper(substr($name, 0, 3)),
            'code' => $attributes['code'] ?? $baseCode.str_pad((string) $sequence, 2, '0', STR_PAD_LEFT),
            'slug' => $slug.'-'.$sequence,
            'federation_name' => $attributes['federation_name'] ?? null,
            'founded_year' => $attributes['founded_year'] ?? null,
            'coach_name' => $attributes['coach_name'] ?? null,
            'team_type' => $attributes['team_type'] ?? 'national',
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    protected function makePlayer(Team $team, string $displayName = 'Test Player', array $attributes = []): Player
    {
        $slug = Str::slug($attributes['slug'] ?? $displayName);
        $sequence = Player::query()->count() + 1;

        return Player::query()->create([
            'team_id' => $attributes['team_id'] ?? $team->id,
            'display_name' => $displayName,
            'first_name' => $attributes['first_name'] ?? Str::before($displayName, ' '),
            'last_name' => $attributes['last_name'] ?? Str::after($displayName, ' '),
            'slug' => $slug.'-'.$sequence,
            'shirt_number' => $attributes['shirt_number'] ?? $sequence,
            'position' => $attributes['position'] ?? 'midfielder',
            'date_of_birth' => $attributes['date_of_birth'] ?? now()->subYears(24)->toDateString(),
            'nationality_code' => $attributes['nationality_code'] ?? 'MA',
            'height_cm' => $attributes['height_cm'] ?? 180,
            'weight_kg' => $attributes['weight_kg'] ?? 75,
            'bio' => $attributes['bio'] ?? null,
            'is_captain' => $attributes['is_captain'] ?? false,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    protected function makeNewsCategory(string $name = 'General News', array $attributes = []): NewsCategory
    {
        $slug = Str::slug($attributes['slug'] ?? $name);
        $sequence = NewsCategory::query()->count() + 1;

        return NewsCategory::query()->create([
            'parent_id' => $attributes['parent_id'] ?? null,
            'name' => $name,
            'slug' => $slug.'-'.$sequence,
            'description' => $attributes['description'] ?? null,
            'status' => $attributes['status'] ?? 'active',
            'sort_order' => $attributes['sort_order'] ?? $sequence,
        ]);
    }

    protected function makeNews(User $author, NewsCategory $category, array $attributes = []): News
    {
        $title = $attributes['title'] ?? 'Test News Item';
        $slug = Str::slug($attributes['slug'] ?? $title);
        $sequence = News::query()->count() + 1;

        return News::query()->create([
            'category_id' => $attributes['category_id'] ?? $category->id,
            'author_id' => $attributes['author_id'] ?? $author->id,
            'editor_id' => $attributes['editor_id'] ?? null,
            'title' => $title,
            'slug' => $slug.'-'.$sequence,
            'summary' => $attributes['summary'] ?? 'Summary for QA coverage.',
            'body' => $attributes['body'] ?? 'Body for QA coverage.',
            'status' => $attributes['status'] ?? 'draft',
            'visibility' => $attributes['visibility'] ?? 'public',
            'published_at' => $attributes['published_at'] ?? null,
            'featured_at' => $attributes['featured_at'] ?? null,
        ]);
    }

    private function makeUserWithRole(string $userType, array $permissionSlugs, array $attributes): User
    {
        $user = User::factory()->create([
            'user_type' => $attributes['user_type'] ?? $userType,
            'status' => $attributes['status'] ?? 'active',
            ...$attributes,
        ]);

        $roleSeed = Str::lower(Str::random(8));
        $role = Role::query()->create([
            'name' => 'Test Role '.Str::upper($roleSeed),
            'slug' => 'test-role-'.$roleSeed,
            'description' => 'Role used for feature testing.',
            'is_system' => false,
        ]);

        foreach ($permissionSlugs as $permissionSlug) {
            $permission = Permission::query()->firstOrCreate(
                ['slug' => $permissionSlug],
                [
                    'name' => 'Permission '.Str::upper(str_replace(['.', '-'], ' ', $permissionSlug)),
                    'module' => 'testing',
                    'description' => 'Permission used for feature testing.',
                ]
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $user->roles()->attach($role->id);

        return $user->fresh(['roles.permissions']);
    }
}
