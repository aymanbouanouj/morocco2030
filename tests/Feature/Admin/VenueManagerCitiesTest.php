<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class VenueManagerCitiesTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_venue_manager_can_access_cities_index_without_fake_links(): void
    {
        $this->makeCity('Rabat', ['slug' => 'rabat', 'code' => 'RAB', 'status' => 'active']);

        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.cities.index'))
            ->assertOk()
            ->assertSee('Cities', false)
            ->assertSee('Create City', false)
            ->assertSee('Rabat', false)
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $html);
    }

    public function test_cities_action_buttons_render_inside_visible_action_cell_wrapper(): void
    {
        $this->makeCity('Marrakech', ['slug' => 'marrakech', 'code' => 'MAR']);

        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.cities.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('admin-venue-table-wrap', $html);
        $this->assertStringContainsString('admin-venue-actions-cell', $html);
        $this->assertStringContainsString('data-testid="city-row-actions"', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Archive', $html);
    }

    public function test_venue_manager_city_search_and_status_filter_return_200(): void
    {
        $this->makeCity('Active City', ['slug' => 'active-city', 'code' => 'ACT', 'status' => 'active']);
        $this->makeCity('Inactive City', ['slug' => 'inactive-city', 'code' => 'INA', 'status' => 'inactive']);

        $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.cities.index', ['search' => 'Active', 'status' => 'active']))
            ->assertOk()
            ->assertSee('Active City', false)
            ->assertDontSee('Inactive City', false);
    }

    public function test_venue_manager_can_create_edit_and_archive_city(): void
    {
        $user = $this->demoUser('venue.manager@morocco2030.test');

        $this->actingAs($user)
            ->get(route('admin.cities.create'))
            ->assertOk()
            ->assertSee('Create City', false);

        $this->actingAs($user)
            ->post(route('admin.cities.store'), [
                'name' => 'Venue Test City',
                'slug' => 'venue-test-city',
                'code' => 'VTC',
                'country_code' => 'MA',
                'region' => 'Test Region',
                'latitude' => '31.6295',
                'longitude' => '-7.9811',
                'description' => 'Created by Venue Manager QA.',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.cities.index'));

        $city = City::query()->where('slug', 'venue-test-city')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.cities.edit', $city))
            ->assertOk()
            ->assertSee('Venue Test City', false);

        $this->actingAs($user)
            ->put(route('admin.cities.update', $city), [
                'name' => 'Venue Test City Updated',
                'slug' => 'venue-test-city',
                'code' => 'VTC',
                'country_code' => 'MA',
                'region' => 'Updated Region',
                'latitude' => '31.6295',
                'longitude' => '-7.9811',
                'description' => 'Updated by Venue Manager QA.',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('admin.cities.index'));

        $this->assertSame('inactive', $city->fresh()->status);

        $this->actingAs($user)
            ->delete(route('admin.cities.destroy', $city))
            ->assertRedirect(route('admin.cities.index'));

        $this->assertSoftDeleted('cities', ['id' => $city->id]);
    }

    private function makeCity(string $name, array $attributes = []): City
    {
        return City::query()->create([
            'name' => $name,
            'slug' => $attributes['slug'] ?? str($name)->slug()->toString(),
            'code' => $attributes['code'] ?? str($name)->substr(0, 3)->upper()->toString(),
            'country_code' => $attributes['country_code'] ?? 'MA',
            'region' => $attributes['region'] ?? 'Test Region',
            'latitude' => $attributes['latitude'] ?? null,
            'longitude' => $attributes['longitude'] ?? null,
            'description' => $attributes['description'] ?? null,
            'status' => $attributes['status'] ?? 'active',
        ]);
    }
}
