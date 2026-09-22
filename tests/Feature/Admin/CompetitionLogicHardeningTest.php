<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\GroupQualificationRule;
use App\Models\MatchFixture;
use App\Models\Team;
use App\Services\Sports\GroupQualificationService;
use App\Services\Sports\StandingsRecalculationService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\FullTournamentStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class CompetitionLogicHardeningTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_full_tournament_structure_seeder_creates_coherent_groups_matches_and_dependencies(): void
    {
        $this->seed(FullTournamentStructureSeeder::class);

        $this->assertSame(12, Group::query()->whereIn('code', range('A', 'L'))->count());
        $this->assertSame(48, Team::query()->whereIn('code', [
            'MOR', 'ESP', 'JPN', 'MEX', 'FRA', 'SEN', 'USA', 'KOR',
            'BRA', 'CRO', 'GHA', 'AUS', 'ARG', 'POR', 'ENG', 'GER',
            'NED', 'URU', 'EGY', 'CAN', 'ITA', 'BEL', 'NGA', 'KSA',
            'COL', 'SUI', 'CMR', 'QAT', 'DEN', 'SWE', 'CHI', 'NZL',
            'NOR', 'POL', 'TUN', 'IRN', 'SRB', 'UKR', 'CRC', 'RSA',
            'AUT', 'TUR', 'ALG', 'JAM', 'WAL', 'PAR', 'MLI', 'CHN',
        ])->count());
        $this->assertSame(72, MatchFixture::query()->where('code', 'like', 'FT-G%')->count());
        $this->assertSame(16, MatchFixture::query()->where('stage_type', 'round_of_32')->where('code', 'like', 'FT-%')->count());
        $this->assertSame(8, MatchFixture::query()->where('stage_type', 'round_of_16')->where('code', 'like', 'FT-%')->count());
        $this->assertSame(4, MatchFixture::query()->where('stage_type', 'quarter_final')->where('code', 'like', 'FT-%')->count());
        $this->assertSame(2, MatchFixture::query()->where('stage_type', 'semi_final')->where('code', 'like', 'FT-%')->count());
        $this->assertSame(1, MatchFixture::query()->where('stage_type', 'third_place')->where('code', 'FT-THIRD')->count());
        $this->assertSame(1, MatchFixture::query()->where('stage_type', 'final')->where('code', 'FT-FINAL')->count());
        $this->assertSame(32, GroupQualificationRule::query()->count());
        $this->assertDatabaseCount('knockout_progressions', 32);

        foreach (Group::query()->whereIn('code', range('A', 'L'))->get() as $group) {
            $this->assertSame(4, $group->teams()->count());
            $this->assertSame(6, $group->matches()->where('stage_type', 'group')->where('code', 'like', 'FT-G'.$group->code.'-%')->count());
        }

        $openingKnockout = MatchFixture::query()
            ->with('targetQualificationRules.group')
            ->where('code', 'FT-R32-01')
            ->firstOrFail();

        $this->assertNull($openingKnockout->home_team_id);
        $this->assertNull($openingKnockout->away_team_id);
        $this->assertSame('Winner of Group A', $openingKnockout->slotLabel('home'));
        $this->assertSame('Runner-up of Group B', $openingKnockout->slotLabel('away'));

        $thirdPlaceSlot = MatchFixture::query()
            ->with('targetQualificationRules.group')
            ->where('code', 'FT-R32-13')
            ->firstOrFail();

        $this->assertSame('Third place of Group A', $thirdPlaceSlot->slotLabel('home'));
    }

    public function test_completed_group_standings_drive_configured_knockout_slots(): void
    {
        $this->seed(FullTournamentStructureSeeder::class);

        $group = Group::query()->where('code', 'A')->firstOrFail();

        MatchFixture::query()
            ->where('group_id', $group->id)
            ->where('stage_type', 'group')
            ->with(['homeTeam', 'awayTeam'])
            ->get()
            ->each(function (MatchFixture $match) {
                $homeCode = $match->homeTeam->code;
                $awayCode = $match->awayTeam->code;

                if ($homeCode === 'MOR' || $awayCode === 'MOR') {
                    $match->forceFill([
                        'status' => 'completed',
                        'home_score' => $homeCode === 'MOR' ? 3 : 0,
                        'away_score' => $awayCode === 'MOR' ? 3 : 0,
                    ])->save();

                    return;
                }

                if ($homeCode === 'ESP' || $awayCode === 'ESP') {
                    $match->forceFill([
                        'status' => 'completed',
                        'home_score' => $homeCode === 'ESP' ? 2 : 0,
                        'away_score' => $awayCode === 'ESP' ? 2 : 0,
                    ])->save();

                    return;
                }

                $match->forceFill([
                    'status' => 'completed',
                    'home_score' => 1,
                    'away_score' => 0,
                ])->save();
            });

        app(StandingsRecalculationService::class)->recalculateGroup($group->fresh('teams'));
        $results = app(GroupQualificationService::class)->applyForGroup($group->fresh([
            'teams',
            'standings.team',
            'qualificationRules.targetMatch',
        ]));

        $morocco = Team::query()->where('code', 'MOR')->firstOrFail();
        $spain = Team::query()->where('code', 'ESP')->firstOrFail();
        $japan = Team::query()->where('code', 'JPN')->firstOrFail();
        $roundOf32One = MatchFixture::query()->where('code', 'FT-R32-01')->firstOrFail();
        $roundOf32Seven = MatchFixture::query()->where('code', 'FT-R32-07')->firstOrFail();
        $roundOf32Thirteen = MatchFixture::query()->where('code', 'FT-R32-13')->firstOrFail();

        $this->assertSame(3, $results->where('changed', true)->count());
        $this->assertSame($morocco->id, $roundOf32One->home_team_id);
        $this->assertNull($roundOf32One->away_team_id);
        $this->assertSame($spain->id, $roundOf32Seven->away_team_id);
        $this->assertSame($japan->id, $roundOf32Thirteen->home_team_id);
        $this->assertDatabaseHas('standings', [
            'group_id' => $group->id,
            'team_id' => $morocco->id,
            'position' => 1,
            'points' => 9,
        ]);
        $this->assertDatabaseHas('group_qualification_rules', [
            'group_id' => $group->id,
            'qualifying_position' => 1,
            'target_match_id' => $roundOf32One->id,
            'team_slot' => 'home',
            'applied_team_id' => $morocco->id,
        ]);
    }

    public function test_default_seeded_database_exposes_full_tournament_structure_publicly_and_in_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(12, Group::query()->whereIn('code', range('A', 'L'))->count());
        $this->assertSame(48, Team::query()->where('status', 'active')->count());
        $this->assertSame(72, MatchFixture::query()->where('stage_type', 'group')->where('code', 'like', 'FT-G%')->count());
        $this->assertSame(32, MatchFixture::query()->whereIn('stage_type', MatchFixture::KNOCKOUT_STAGE_TYPES)->where('code', 'like', 'FT-%')->count());
        $this->assertSame(48, \App\Models\Standing::query()->count());
        $this->assertSame(32, GroupQualificationRule::query()->count());

        $morocco = Team::query()->where('code', 'MOR')->firstOrFail();

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Algeria')
            ->assertSee('Argentina')
            ->assertSee('China');

        $this->get(route('teams.show', $morocco->slug))
            ->assertOk()
            ->assertSee('Morocco')
            ->assertSee('Group A');

        $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee('Group A')
            ->assertSee('Group L')
            ->assertSee('Morocco')
            ->assertSee('China');

        $this->get(route('matches.index'))
            ->assertOk()
            ->assertSee('Group A')
            ->assertSee('Round of 32')
            ->assertSee('Final')
            ->assertSee('VS');

        $this->get(route('knockout.index'))
            ->assertOk()
            ->assertSee('Round of 32')
            ->assertSee('Winner of Group A')
            ->assertSee('Runner-up of Group B')
            ->assertSee('Final');

        $admin = $this->makeStaffUser(['groups.manage', 'teams.manage', 'matches.manage']);

        $this->actingAs($admin)
            ->get(route('admin.groups.index'))
            ->assertOk()
            ->assertSee('Group L')
            ->assertSee('4')
            ->assertSee('6');

        $this->actingAs($admin)
            ->get(route('admin.teams.index', ['search' => 'Morocco']))
            ->assertOk()
            ->assertSee('Morocco')
            ->assertSee('Group A');

        $this->actingAs($admin)
            ->get(route('admin.matches.index'))
            ->assertOk()
            ->assertSee('FT-GA-01')
            ->assertSee('FT-FINAL')
            ->assertSee('Winner of FT-SF-01')
            ->assertSee('Winner of FT-SF-02');
    }
}
