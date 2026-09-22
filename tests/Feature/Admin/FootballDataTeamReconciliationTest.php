<?php

namespace Tests\Feature\Admin;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataTeamReconciliationTest extends TestCase
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

    public function test_super_admin_can_run_team_reconciliation(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk()
            ->assertSee('Team reconciliation summary')
            ->assertSee('Final Real Fd Team Count')
            ->assertSee('48')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_journalist_cannot_run_team_reconciliation(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertForbidden();
    }

    public function test_exact_confirmation_required(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.reconcile-teams'), [
                'team_reconciliation_confirmation' => 'RECONCILE TEAMS',
                'understands_team_reconciliation' => '1',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('team_reconciliation_confirmation');
    }

    public function test_checkbox_required(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.reconcile-teams'), [
                'team_reconciliation_confirmation' => 'RECONCILE FOOTBALL TEAMS',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('understands_team_reconciliation');
    }

    public function test_token_never_displayed(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk()
            ->assertDontSee('test-token-not-secret');
    }

    public function test_48_source_teams_are_matched_and_upgraded_without_creating_duplicates(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $teamCount = Team::query()->count();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk()
            ->assertSee('Upgraded Old Showcase Teams')
            ->assertSee('48')
            ->assertSee('Created')
            ->assertSee('0');

        $this->assertSame($teamCount, Team::query()->count());
        $this->assertSame(48, $this->realFdTeamCount());
    }

    public function test_existing_old_showcase_matching_team_is_upgraded_instead_of_duplicated(): void
    {
        $group = $this->makeGroup('FDX');
        $oldTeam = $this->makeTeam($group, 'Source Team 01', ['code' => 'T01']);
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        $this->assertSame(48, $this->realFdTeamCount());
        $this->assertDatabaseHas('teams', ['id' => $oldTeam->id]);
        $this->assertSame('football-data.org', data_get($oldTeam->fresh()->meta, 'source'));
        $this->assertSame(1001, data_get($oldTeam->fresh()->meta, 'football_data.id'));
        $this->assertSame(48, Team::query()->count());
    }

    public function test_placeholder_teams_are_not_converted_into_real_teams(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $placeholder = $this->makePlaceholderTeam();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk()
            ->assertSee('Placeholders Preserved')
            ->assertSee('1');

        $this->assertTrue((bool) data_get($placeholder->fresh()->meta, 'placeholder'));
        $this->assertSame('football-data.org', data_get($placeholder->fresh()->meta, 'source'));
    }

    public function test_final_real_fd_team_count_is_48(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        $this->assertSame(48, $this->realFdTeamCount());
    }

    public function test_duplicate_source_ids_are_not_created(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        $duplicates = Team::query()
            ->where('meta->source', 'football-data.org')
            ->get()
            ->groupBy(fn (Team $team) => (string) data_get($team->meta, 'football_data.id'))
            ->filter(fn ($items, $key) => $key !== '' && $items->count() > 1);

        $this->assertCount(0, $duplicates);
    }

    public function test_duplicate_tla_codes_are_avoided(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        $duplicates = Team::query()
            ->where('meta->source', 'football-data.org')
            ->get()
            ->groupBy(fn (Team $team) => (string) data_get($team->meta, 'football_data.tla'))
            ->filter(fn ($items, $key) => $key !== '' && $items->count() > 1);

        $this->assertCount(0, $duplicates);
    }

    public function test_crest_url_stored_in_team_meta(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        $team = Team::query()->where('meta->football_data->id', 1001)->firstOrFail();

        $this->assertSame('https://crests.example/t01.svg', data_get($team->meta, 'football_data.crest'));
    }

    public function test_squad_dry_run_matches_teams_by_meta_football_data_id(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        Http::fake($this->squadDryRunFakeResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('Matched Local Teams')
            ->assertSee('48');
    }

    public function test_squad_dry_run_skips_placeholders_after_reconciliation(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->makePlaceholderTeam();
        $this->fakeTeamApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        Http::fake($this->squadDryRunFakeResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('Skipped Placeholders')
            ->assertSee('1');
    }

    public function test_no_player_records_or_coach_data_are_written(): void
    {
        $this->makeOldShowcaseTeamsForSource();
        $this->fakeTeamApi();
        $playerCount = Player::query()->count();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk();

        $this->assertSame($playerCount, Player::query()->count());
        $this->assertSame(0, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
    }

    public function test_safe_api_429_handling(): void
    {
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response(['message' => 'Rate limit exceeded'], 429),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile-teams'), $this->validRequest())
            ->assertOk()
            ->assertSee('API request failed with status 429: Rate limit exceeded')
            ->assertDontSee('test-token-not-secret');
    }

    protected function configureFootballData(): void
    {
        config([
            'services.external_football.provider' => 'football-data',
            'services.external_football.base_url' => 'https://api.football-data.org/v4',
            'services.external_football.token' => 'test-token-not-secret',
            'services.external_football.timeout' => 15,
        ]);
    }

    protected function validRequest(): array
    {
        return [
            'team_reconciliation_confirmation' => 'RECONCILE FOOTBALL TEAMS',
            'understands_team_reconciliation' => '1',
        ];
    }

    protected function fakeTeamApi(): void
    {
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response($this->worldCupTeamsPayload(), 200),
        ]);
    }

    protected function squadDryRunFakeResponses(): array
    {
        $responses = [
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response($this->worldCupTeamsPayload(), 200),
        ];

        foreach ($this->sourceTeams() as $team) {
            $responses['https://api.football-data.org/v4/teams/'.$team['id']] = Http::response([
                'id' => $team['id'],
                'name' => $team['name'],
                'tla' => $team['tla'],
                'squad' => [],
                'coach' => null,
            ], 200);
        }

        return $responses;
    }

    protected function makeOldShowcaseTeamsForSource(): void
    {
        $group = $this->makeGroup('FDX');

        foreach ($this->sourceTeams() as $team) {
            $localTeam = $this->makeTeam($group, $team['name'], ['code' => $team['tla']]);
            $localTeam->update([
                'meta' => [
                    'football_data' => [
                        'id' => $team['id'],
                    ],
                ],
            ]);
        }
    }

    protected function makePlaceholderTeam(): Team
    {
        $team = $this->makeTeam($this->makeGroup('TB1'), 'TBD Home FD-WC-999999', ['code' => 'FDH999999']);
        $team->update([
            'meta' => [
                'source' => 'football-data.org',
                'source_competition' => 'WC',
                'placeholder' => true,
            ],
        ]);

        return $team->fresh();
    }

    protected function worldCupTeamsPayload(): array
    {
        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
            'teams' => $this->sourceTeams(),
        ];
    }

    protected function sourceTeams(): array
    {
        return collect(range(1, 48))
            ->map(fn (int $number) => [
                'id' => 1000 + $number,
                'name' => 'Source Team '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'shortName' => 'Team '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'tla' => 'T'.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'crest' => 'https://crests.example/t'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'.svg',
            ])
            ->all();
    }

    protected function realFdTeamCount(): int
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where(function ($query) {
                $query->whereNull('meta->placeholder')
                    ->orWhere('meta->placeholder', false);
            })
            ->count();
    }
}
