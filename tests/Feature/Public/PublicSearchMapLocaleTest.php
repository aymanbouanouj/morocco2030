<?php

namespace Tests\Feature\Public;

use App\Models\Language;
use App\Models\MatchFixture;
use App\Models\Partner;
use App\Models\Translation;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicSearchMapLocaleTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_public_search_returns_grouped_results_from_real_content_types(): void
    {
        $author = $this->makeStaffUser();
        $category = $this->makeNewsCategory('Atlas Updates');
        $group = $this->makeGroup('A');
        $city = $this->makeCity('Atlas City', [
            'region' => 'Atlas Region',
            'description' => 'Atlas host city overview.',
        ]);
        $stadium = $this->makeStadium($city, 'Atlas Arena', [
            'address' => 'Atlas Boulevard',
        ]);
        $team = $this->makeTeam($group, 'Atlas Lions', [
            'code' => 'ATL',
            'coach_name' => 'Atlas Coach',
        ]);
        $awayTeam = $this->makeTeam($group, 'Coastal Stars', [
            'code' => 'CST',
        ]);
        $player = $this->makePlayer($team, 'Atlas Captain', [
            'bio' => 'Atlas midfield leader.',
        ]);

        $this->makeNews($author, $category, [
            'title' => 'Atlas Operations Briefing',
            'summary' => 'Atlas tournament operations update.',
            'body' => 'Atlas body copy for public search.',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now()->subHour(),
        ]);

        Partner::query()->create([
            'name' => 'Atlas Mobility',
            'slug' => 'atlas-mobility',
            'category' => 'Mobility Partner',
            'tier' => 'Official',
            'status' => 'active',
        ]);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $team->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'ATL-01',
            'slug' => 'atlas-lions-vs-coastal-stars',
            'stage_type' => 'group',
            'match_date' => now()->addDay(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'scheduled',
        ]);

        $this->get(route('search.index', ['q' => 'Atlas']))
            ->assertOk()
            ->assertSee('Atlas Operations Briefing')
            ->assertSee('Atlas Lions')
            ->assertSee('Atlas Captain')
            ->assertSee('Atlas City')
            ->assertSee('Atlas Arena')
            ->assertSee('Atlas Mobility')
            ->assertSee('ATL-01');
    }

    public function test_map_page_handles_missing_coordinates_with_list_fallback(): void
    {
        $cityWithCoordinates = $this->makeCity('Casablanca', [
            'latitude' => 33.5731,
            'longitude' => -7.5898,
        ]);
        $cityWithoutCoordinates = $this->makeCity('Meknes');

        $stadiumWithCoordinates = $this->makeStadium($cityWithCoordinates, 'Coordinate Stadium', [
            'latitude' => 33.5800,
            'longitude' => -7.6000,
        ]);
        $this->makeStadium($cityWithoutCoordinates, 'Pending Stadium');

        $this->get(route('map.index'))
            ->assertOk()
            ->assertSee('Interactive Host Map')
            ->assertSee('id="host-map"', false)
            ->assertSee('data-map-filter="city"', false)
            ->assertSee('data-map-filter="stadium"', false)
            ->assertSee('"id":"city-'.$cityWithCoordinates->id.'"', false)
            ->assertSee('"id":"stadium-', false)
            ->assertSee('View on map')
            ->assertSee(route('cities.show', $cityWithCoordinates->slug), false)
            ->assertSee(route('stadiums.show', $stadiumWithCoordinates->slug), false)
            ->assertSee($cityWithCoordinates->name)
            ->assertSee($cityWithoutCoordinates->name)
            ->assertSee('Map point coming soon')
            ->assertSee('Locations Awaiting Map Coordinates')
            ->assertSee(route('cities.show', $cityWithoutCoordinates->slug), false);
    }

    public function test_public_language_switching_updates_locale_context_without_breaking_routes(): void
    {
        $this->seed(LanguageSeeder::class);

        $city = $this->makeCity('Rabat', [
            'description' => 'English city description.',
        ]);

        $french = Language::query()->where('code', 'fr')->firstOrFail();

        Translation::query()->create([
            'translatable_type' => $city::class,
            'translatable_id' => $city->id,
            'language_id' => $french->id,
            'field' => 'description',
            'value' => 'Description francaise de la ville.',
        ]);

        $this->from(route('cities.show', $city->slug))
            ->get(route('language.switch', $french->code))
            ->assertRedirect(route('cities.show', $city->slug));

        $this->get(route('cities.show', $city->slug))
            ->assertOk()
            ->assertSee('Description francaise de la ville.')
            ->assertSee('lang="fr-MA"', false);
    }

    public function test_public_language_switching_rejects_external_previous_urls(): void
    {
        $this->seed(LanguageSeeder::class);

        $french = Language::query()->where('code', 'fr')->firstOrFail();

        $this->from('https://external.example/credential-capture')
            ->get(route('language.switch', $french->code))
            ->assertRedirect(route('home'));
    }
}
