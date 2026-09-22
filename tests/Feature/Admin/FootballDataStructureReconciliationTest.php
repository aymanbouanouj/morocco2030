<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataStructureReconciliationTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
        $this->configureFootballData();
    }

    public function test_super_admin_can_run_structure_reconciliation(): void
    {
        $payload = $this->seedImportedWorldCupStructure();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-structure'), $this->validRequest())
            ->assertOk()
            ->assertSee('Structure reconciliation summary')
            ->assertSee('Groups Detected')
            ->assertSee('12')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_journalist_cannot_run_structure_reconciliation(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-structure'), $this->validRequest())
            ->assertForbidden();
    }

    public function test_structure_reconciliation_requires_exact_confirmation(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.reconcile-structure'), [
                'structure_confirmation' => 'RECONCILE STRUCTURE',
                'understands_structure' => '1',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('structure_confirmation');
    }

    public function test_structure_reconciliation_requires_checkbox(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.reconcile-structure'), [
                'structure_confirmation' => 'RECONCILE FOOTBALL STRUCTURE',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('understands_structure');
    }

    public function test_api_groups_are_stored_and_matches_are_assigned_to_correct_groups(): void
    {
        $payload = $this->seedImportedWorldCupStructure();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-structure'), $this->validRequest())
            ->assertOk();

        $groupA = Group::query()->where('code', 'A')->firstOrFail();
        $groupB = Group::query()->where('code', 'B')->firstOrFail();
        $groupL = Group::query()->where('code', 'L')->firstOrFail();
        $matchA = MatchFixture::query()->where('code', 'FD-WC-537327')->firstOrFail();
        $matchB = MatchFixture::query()->where('code', 'FD-WC-537333')->firstOrFail();
        $knockout = MatchFixture::query()->where('code', 'FD-WC-537417')->firstOrFail();

        $this->assertSame('GROUP_A', data_get($matchA->meta, 'football_data.group'));
        $this->assertSame('Group A', data_get($matchA->meta, 'football_data.group_label'));
        $this->assertSame('GROUP_STAGE', data_get($matchA->meta, 'football_data.stage'));
        $this->assertSame('Group Stage', data_get($matchA->meta, 'football_data.stage_label'));
        $this->assertSame($groupA->id, $matchA->group_id);
        $this->assertSame($groupB->id, $matchB->group_id);
        $this->assertSame('round_of_32', $knockout->stage_type);
        $this->assertNull($knockout->group_id);
        $this->assertNull(data_get($knockout->meta, 'football_data.group'));
        $this->assertSame('LAST_32', data_get($knockout->meta, 'football_data.stage'));
        $this->assertNotNull($groupL);
    }

    public function test_structure_reconciliation_creates_or_matches_multiple_api_groups_without_foreign_venues(): void
    {
        $payload = $this->seedImportedWorldCupStructure();
        $stadiumCount = Stadium::query()->count();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-structure'), $this->validRequest())
            ->assertOk()
            ->assertSee('Groups Created')
            ->assertSee('Groups Matched')
            ->assertSee('Knockout Unassigned Matches');

        $this->assertSame(12, Group::query()->whereIn('code', range('A', 'L'))->count());
        $this->assertSame($stadiumCount, Stadium::query()->count());
        $this->assertSame(12, MatchFixture::query()
            ->where('code', 'like', 'FD-WC-%')
            ->whereNotNull('group_id')
            ->distinct('group_id')
            ->count('group_id'));
    }

    public function test_structure_reconciliation_blocks_when_api_has_no_groups(): void
    {
        $payload = $this->seedImportedWorldCupStructure(includeGroups: false);
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-structure'), $this->validRequest())
            ->assertOk()
            ->assertSee('did not provide group values')
            ->assertDontSee('test-token-not-secret');
    }

    private function configureFootballData(): void
    {
        config([
            'services.external_football.provider' => 'football-data',
            'services.external_football.base_url' => 'https://api.football-data.org/v4',
            'services.external_football.token' => 'test-token-not-secret',
            'services.external_football.timeout' => 15,
        ]);
    }

    private function validRequest(): array
    {
        return [
            'structure_confirmation' => 'RECONCILE FOOTBALL STRUCTURE',
            'understands_structure' => '1',
        ];
    }

    private function seedImportedWorldCupStructure(bool $includeGroups = true): array
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $defaultGroup = $this->makeGroup('A');
        $teamOne = $this->makeTeam($defaultGroup, 'Team One', ['code' => 'ONE']);
        $teamTwo = $this->makeTeam($defaultGroup, 'Team Two', ['code' => 'TWO']);
        $matches = [];
        $sourceId = 537327;
        $usedIds = [];

        foreach (range('A', 'L') as $groupCode) {
            for ($i = 1; $i <= 6; $i++) {
                if ($groupCode === 'A' && $i === 1) {
                    $id = 537327;
                } elseif ($groupCode === 'B' && $i === 1) {
                    $id = 537333;
                } else {
                    do {
                        $id = ++$sourceId;
                    } while (in_array($id, $usedIds, true) || $id === 537417);
                }

                $usedIds[] = $id;
                $sourceGroup = $includeGroups ? 'GROUP_'.$groupCode : null;
                $matches[] = $this->sourceMatch($id, 'GROUP_STAGE', $sourceGroup, 1, 'TIMED', $teamOne->name, $teamTwo->name);
            }
        }

        $matches[] = $this->sourceMatch(537417, 'LAST_32', null, null, 'TIMED', null, null);
        $usedIds[] = 537417;

        while (count($matches) < 104) {
            do {
                $id = ++$sourceId;
            } while (in_array($id, $usedIds, true));

            $usedIds[] = $id;
            $matches[] = $this->sourceMatch($id, 'LAST_32', null, null, 'TIMED', null, null);
        }

        foreach ($matches as $sourceMatch) {
            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $defaultGroup->id,
                'home_team_id' => $teamOne->id,
                'away_team_id' => $teamTwo->id,
                'code' => 'FD-WC-'.$sourceMatch['id'],
                'slug' => Str::slug('FD-WC-'.$sourceMatch['id']),
                'stage_type' => 'group',
                'round_number' => 1,
                'match_date' => $sourceMatch['utcDate'],
                'timezone' => 'UTC',
                'status' => 'scheduled',
                'published_at' => now(),
                'meta' => ['source' => 'football-data.org', 'source_match_id' => $sourceMatch['id']],
            ]);
        }

        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
            'matches' => $matches,
        ];
    }

    private function sourceMatch(
        int $id,
        string $stage,
        ?string $group,
        ?int $matchday,
        string $status,
        ?string $homeName,
        ?string $awayName
    ): array {
        return [
            'id' => $id,
            'utcDate' => now()->addDays($id % 90)->utc()->format('Y-m-d\TH:i:s\Z'),
            'status' => $status,
            'stage' => $stage,
            'group' => $group,
            'matchday' => $matchday,
            'homeTeam' => ['id' => $homeName ? $id : null, 'name' => $homeName, 'tla' => $homeName ? 'ONE' : null],
            'awayTeam' => ['id' => $awayName ? $id + 1 : null, 'name' => $awayName, 'tla' => $awayName ? 'TWO' : null],
            'score' => ['fullTime' => ['home' => null, 'away' => null]],
        ];
    }
}
