<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class VenueManagerStadiumsTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_venue_manager_can_access_stadiums_index_without_fake_links(): void
    {
        $city = $this->makeCity('Casablanca', ['slug' => 'casablanca', 'code' => 'CAS']);
        $this->makeStadium($city, 'Grand Stade Hassan II', ['slug' => 'grand-stade-hassan-ii', 'code' => 'GSH2']);

        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.stadiums.index'))
            ->assertOk()
            ->assertSee('Stadiums', false)
            ->assertSee('Create Stadium', false)
            ->assertSee('Grand Stade Hassan II', false)
            ->assertSee('Casablanca', false)
            ->assertSee('115,000', false)
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $html);
    }

    public function test_stadiums_action_buttons_render_inside_visible_action_cell_wrapper(): void
    {
        $city = $this->makeCity('Tangier', ['slug' => 'tangier', 'code' => 'TAN']);
        $this->makeStadium($city, 'Tangier Stadium', ['slug' => 'tangier-stadium', 'code' => 'TANST']);

        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.stadiums.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('admin-venue-table-wrap', $html);
        $this->assertStringContainsString('admin-venue-actions-cell', $html);
        $this->assertStringContainsString('data-testid="stadium-row-actions"', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Archive', $html);
    }

    public function test_venue_manager_stadium_search_and_status_filter_return_200(): void
    {
        $city = $this->makeCity('Rabat', ['slug' => 'rabat', 'code' => 'RAB']);
        $this->makeStadium($city, 'Active Venue', ['slug' => 'active-venue', 'code' => 'ACV', 'status' => 'active']);
        $this->makeStadium($city, 'Inactive Venue', ['slug' => 'inactive-venue', 'code' => 'INV', 'status' => 'inactive']);

        $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.stadiums.index', ['search' => 'Active', 'status' => 'active']))
            ->assertOk()
            ->assertSee('Active Venue', false)
            ->assertDontSee('Inactive Venue', false);
    }

    public function test_venue_manager_can_create_edit_and_archive_stadium(): void
    {
        $user = $this->demoUser('venue.manager@morocco2030.test');
        $city = $this->makeCity('Marrakech', ['slug' => 'marrakech', 'code' => 'MAR']);

        $this->actingAs($user)
            ->get(route('admin.stadiums.create'))
            ->assertOk()
            ->assertSee('Create Stadium', false);

        $this->actingAs($user)
            ->post(route('admin.stadiums.store'), [
                'city_id' => $city->id,
                'name' => 'Venue Test Stadium',
                'slug' => 'venue-test-stadium',
                'code' => 'VTS',
                'capacity' => 45000,
                'address' => 'Venue QA Street',
                'latitude' => '31.6295',
                'longitude' => '-7.9811',
                'opened_year' => 2030,
                'surface_type' => 'grass',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.stadiums.index'));

        $stadium = Stadium::query()->where('slug', 'venue-test-stadium')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.stadiums.edit', $stadium))
            ->assertOk()
            ->assertSee('Venue Test Stadium', false);

        $this->actingAs($user)
            ->put(route('admin.stadiums.update', $stadium), [
                'city_id' => $city->id,
                'name' => 'Venue Test Stadium Updated',
                'slug' => 'venue-test-stadium',
                'code' => 'VTS',
                'capacity' => 46000,
                'address' => 'Venue QA Street Updated',
                'latitude' => '31.6295',
                'longitude' => '-7.9811',
                'opened_year' => 2030,
                'surface_type' => 'hybrid',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('admin.stadiums.index'));

        $this->assertSame('inactive', $stadium->fresh()->status);
        $this->assertSame(46000, $stadium->fresh()->capacity);

        $this->actingAs($user)
            ->delete(route('admin.stadiums.destroy', $stadium))
            ->assertRedirect(route('admin.stadiums.index'));

        $this->assertSoftDeleted('stadiums', ['id' => $stadium->id]);
    }

    private function makeCity(string $name, array $attributes = []): City
    {
        return City::query()->create([
            'name' => $name,
            'slug' => $attributes['slug'] ?? str($name)->slug()->toString(),
            'code' => $attributes['code'] ?? str($name)->substr(0, 3)->upper()->toString(),
            'country_code' => $attributes['country_code'] ?? 'MA',
            'region' => $attributes['region'] ?? 'Test Region',
            'status' => $attributes['status'] ?? 'active',
        ]);
    }

    private function makeStadium(City $city, string $name, array $attributes = []): Stadium
    {
        return Stadium::query()->create([
            'city_id' => $city->id,
            'name' => $name,
            'slug' => $attributes['slug'] ?? str($name)->slug()->toString(),
            'code' => $attributes['code'] ?? str($name)->substr(0, 5)->upper()->toString(),
            'capacity' => $attributes['capacity'] ?? 115000,
            'address' => $attributes['address'] ?? 'Test address',
            'latitude' => $attributes['latitude'] ?? null,
            'longitude' => $attributes['longitude'] ?? null,
            'opened_year' => $attributes['opened_year'] ?? null,
            'surface_type' => $attributes['surface_type'] ?? 'grass',
            'status' => $attributes['status'] ?? 'active',
        ]);
    }
}
