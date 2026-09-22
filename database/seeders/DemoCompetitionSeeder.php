<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Group;
use App\Models\KnockoutProgression;
use App\Models\MatchFixture;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCompetitionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $groupA = Group::query()->updateOrCreate(
                ['code' => 'A'],
                ['name' => 'Group A', 'sort_order' => 1]
            );

            $groupB = Group::query()->updateOrCreate(
                ['code' => 'B'],
                ['name' => 'Group B', 'sort_order' => 2]
            );

            $casablanca = City::query()->updateOrCreate(
                ['slug' => 'casablanca-demo'],
                [
                    'name' => 'Casablanca',
                    'code' => 'CAS',
                    'country_code' => 'MA',
                    'region' => 'Casablanca-Settat',
                    'description' => 'Demo city for admin smoke tests.',
                    'status' => 'active',
                ]
            );

            $rabat = City::query()->updateOrCreate(
                ['slug' => 'rabat-demo'],
                [
                    'name' => 'Rabat',
                    'code' => 'RAB',
                    'country_code' => 'MA',
                    'region' => 'Rabat-Sale-Kenitra',
                    'description' => 'Demo city for admin smoke tests.',
                    'status' => 'active',
                ]
            );

            $mohammedV = Stadium::query()->updateOrCreate(
                ['slug' => 'mohammed-v-demo'],
                [
                    'city_id' => $casablanca->id,
                    'name' => 'Mohammed V Stadium',
                    'code' => 'ST-CAS',
                    'capacity' => 67000,
                    'address' => 'Casablanca, Morocco',
                    'surface_type' => 'grass',
                    'status' => 'active',
                ]
            );

            $princeMoulay = Stadium::query()->updateOrCreate(
                ['slug' => 'prince-moulay-abdellah-demo'],
                [
                    'city_id' => $rabat->id,
                    'name' => 'Prince Moulay Abdellah Stadium',
                    'code' => 'ST-RAB',
                    'capacity' => 53000,
                    'address' => 'Rabat, Morocco',
                    'surface_type' => 'grass',
                    'status' => 'active',
                ]
            );

            $morocco = $this->upsertTeam($groupA, 'Morocco', 'MOR', 'morocco-demo', 'Walid Regragui');
            $spain = $this->upsertTeam($groupA, 'Spain', 'ESP', 'spain-demo', 'Luis de la Fuente');
            $brazil = $this->upsertTeam($groupB, 'Brazil', 'BRA', 'brazil-demo', 'Dorival Junior');
            $france = $this->upsertTeam($groupB, 'France', 'FRA', 'france-demo', 'Didier Deschamps');

            $this->seedPlayers($morocco, [
                ['Yassine Bounou', 'goalkeeper', 1],
                ['Achraf Hakimi', 'defender', 2],
                ['Sofyan Amrabat', 'midfielder', 4],
                ['Hakim Ziyech', 'forward', 7],
                ['Youssef En-Nesyri', 'forward', 9],
            ]);

            $this->seedPlayers($spain, [
                ['Unai Simon', 'goalkeeper', 1],
                ['Dani Carvajal', 'defender', 2],
                ['Rodri Hernandez', 'midfielder', 16],
                ['Pedri Gonzalez', 'midfielder', 20],
                ['Alvaro Morata', 'forward', 7],
            ]);

            $this->seedPlayers($brazil, [
                ['Alisson Becker', 'goalkeeper', 1],
                ['Danilo Luiz', 'defender', 2],
                ['Bruno Guimaraes', 'midfielder', 5],
                ['Vinicius Junior', 'forward', 7],
                ['Rodrygo Goes', 'forward', 10],
            ]);

            $this->seedPlayers($france, [
                ['Mike Maignan', 'goalkeeper', 16],
                ['Jules Kounde', 'defender', 5],
                ['Aurelien Tchouameni', 'midfielder', 8],
                ['Antoine Griezmann', 'forward', 7],
                ['Kylian Mbappe', 'forward', 10],
            ]);

            $groupMatch = MatchFixture::query()->updateOrCreate(
                ['code' => 'GRP-A-01'],
                [
                    'stadium_id' => $mohammedV->id,
                    'city_id' => $casablanca->id,
                    'group_id' => $groupA->id,
                    'home_team_id' => $morocco->id,
                    'away_team_id' => $spain->id,
                    'slug' => 'grp-a-01-demo',
                    'stage_type' => 'group',
                    'round_number' => 1,
                    'match_date' => now()->subDay(),
                    'timezone' => 'Africa/Casablanca',
                    'status' => 'completed',
                    'attendance' => 61000,
                    'home_score' => 2,
                    'away_score' => 1,
                    'home_penalty_score' => null,
                    'away_penalty_score' => null,
                    'extra_time_played' => false,
                ]
            );

            MatchFixture::query()->updateOrCreate(
                ['code' => 'GRP-B-01'],
                [
                    'stadium_id' => $princeMoulay->id,
                    'city_id' => $rabat->id,
                    'group_id' => $groupB->id,
                    'home_team_id' => $brazil->id,
                    'away_team_id' => $france->id,
                    'slug' => 'grp-b-01-demo',
                    'stage_type' => 'group',
                    'round_number' => 1,
                    'match_date' => now()->addDay(),
                    'timezone' => 'Africa/Casablanca',
                    'status' => 'scheduled',
                    'attendance' => null,
                    'home_score' => null,
                    'away_score' => null,
                    'home_penalty_score' => null,
                    'away_penalty_score' => null,
                    'extra_time_played' => false,
                ]
            );

            $quarterFinal = MatchFixture::query()->updateOrCreate(
                ['code' => 'QF-01'],
                [
                    'stadium_id' => $mohammedV->id,
                    'city_id' => $casablanca->id,
                    'group_id' => null,
                    'home_team_id' => $morocco->id,
                    'away_team_id' => $brazil->id,
                    'slug' => 'qf-01-demo',
                    'stage_type' => 'quarter_final',
                    'round_number' => 1,
                    'match_date' => now()->subHours(4),
                    'timezone' => 'Africa/Casablanca',
                    'status' => 'completed',
                    'attendance' => 64000,
                    'home_score' => 1,
                    'away_score' => 1,
                    'home_penalty_score' => 4,
                    'away_penalty_score' => 3,
                    'extra_time_played' => true,
                ]
            );

            $quarterFinalTwo = MatchFixture::query()->updateOrCreate(
                ['code' => 'QF-02'],
                [
                    'stadium_id' => $princeMoulay->id,
                    'city_id' => $rabat->id,
                    'group_id' => null,
                    'home_team_id' => $france->id,
                    'away_team_id' => $spain->id,
                    'slug' => 'qf-02-demo',
                    'stage_type' => 'quarter_final',
                    'round_number' => 2,
                    'match_date' => now()->subHours(2),
                    'timezone' => 'Africa/Casablanca',
                    'status' => 'completed',
                    'attendance' => 59000,
                    'home_score' => 2,
                    'away_score' => 0,
                    'home_penalty_score' => null,
                    'away_penalty_score' => null,
                    'extra_time_played' => false,
                ]
            );

            $semiFinal = MatchFixture::query()->updateOrCreate(
                ['code' => 'SF-01'],
                [
                    'stadium_id' => $princeMoulay->id,
                    'city_id' => $rabat->id,
                    'group_id' => null,
                    'home_team_id' => null,
                    'away_team_id' => null,
                    'slug' => 'sf-01-demo',
                    'stage_type' => 'semi_final',
                    'round_number' => 1,
                    'match_date' => now()->addDays(2),
                    'timezone' => 'Africa/Casablanca',
                    'status' => 'scheduled',
                    'attendance' => null,
                    'home_score' => null,
                    'away_score' => null,
                    'home_penalty_score' => null,
                    'away_penalty_score' => null,
                    'extra_time_played' => false,
                ]
            );

            KnockoutProgression::query()->updateOrCreate(
                [
                    'source_match_id' => $quarterFinal->id,
                    'target_match_id' => $semiFinal->id,
                    'progression_type' => 'winner',
                    'team_slot' => 'home',
                ],
                ['notes' => 'Demo winner progression for smoke tests.']
            );

            KnockoutProgression::query()->updateOrCreate(
                [
                    'source_match_id' => $quarterFinalTwo->id,
                    'target_match_id' => $semiFinal->id,
                    'progression_type' => 'winner',
                    'team_slot' => 'away',
                ],
                ['notes' => 'Demo second slot progression for smoke tests.']
            );
        });
    }

    private function upsertTeam(Group $group, string $name, string $code, string $slug, string $coachName): Team
    {
        return Team::query()->updateOrCreate(
            ['code' => $code],
            [
                'group_id' => $group->id,
                'name' => $name,
                'short_name' => $code,
                'slug' => $slug,
                'federation_name' => $name.' Football Federation',
                'coach_name' => $coachName,
                'team_type' => 'national',
                'status' => 'active',
            ]
        );
    }

    private function seedPlayers(Team $team, array $players): void
    {
        foreach ($players as [$displayName, $position, $shirtNumber]) {
            Player::query()->updateOrCreate(
                ['slug' => str($displayName)->slug()->append('-demo')->toString()],
                [
                    'team_id' => $team->id,
                    'display_name' => $displayName,
                    'first_name' => str($displayName)->before(' ')->toString(),
                    'last_name' => str($displayName)->after(' ')->toString(),
                    'shirt_number' => $shirtNumber,
                    'position' => $position,
                    'date_of_birth' => now()->subYears(25)->toDateString(),
                    'nationality_code' => 'MA',
                    'height_cm' => 182,
                    'weight_kg' => 76,
                    'status' => 'active',
                    'is_captain' => false,
                ]
            );
        }
    }
}
