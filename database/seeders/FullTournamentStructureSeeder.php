<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Group;
use App\Models\GroupQualificationRule;
use App\Models\KnockoutProgression;
use App\Models\MatchFixture;
use App\Models\Stadium;
use App\Models\Standing;
use App\Models\Team;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FullTournamentStructureSeeder extends Seeder
{
    private const TIMEZONE = 'Africa/Casablanca';

    public function run(): void
    {
        DB::transaction(function () {
            $cities = $this->seedCities();
            $stadiums = $this->seedStadiums($cities);
            $groups = $this->seedGroups();
            $teams = $this->seedTeams($groups);
            $matches = $this->seedGroupMatches($groups, $teams, $cities, $stadiums);
            $matches = array_merge($matches, $this->seedKnockoutMatches($cities, $stadiums));

            $this->seedInitialStandings($groups);
            $this->seedGroupQualificationRules($groups, $matches);
            $this->seedKnockoutProgressions($matches);
        });
    }

    private function seedCities(): array
    {
        $definitions = [
            ['code' => 'CAS', 'name' => 'Casablanca', 'slug' => 'casablanca', 'region' => 'Casablanca-Settat', 'lat' => 33.5731, 'lng' => -7.5898],
            ['code' => 'RAB', 'name' => 'Rabat', 'slug' => 'rabat', 'region' => 'Rabat-Sale-Kenitra', 'lat' => 34.0209, 'lng' => -6.8416],
            ['code' => 'TNG', 'name' => 'Tangier', 'slug' => 'tangier', 'region' => 'Tanger-Tetouan-Al Hoceima', 'lat' => 35.7595, 'lng' => -5.8340],
            ['code' => 'MRK', 'name' => 'Marrakech', 'slug' => 'marrakech', 'region' => 'Marrakech-Safi', 'lat' => 31.6295, 'lng' => -7.9811],
            ['code' => 'AGD', 'name' => 'Agadir', 'slug' => 'agadir', 'region' => 'Souss-Massa', 'lat' => 30.4278, 'lng' => -9.5981],
            ['code' => 'FES', 'name' => 'Fes', 'slug' => 'fes', 'region' => 'Fes-Meknes', 'lat' => 34.0181, 'lng' => -5.0078],
            ['code' => 'OUJ', 'name' => 'Oujda', 'slug' => 'oujda', 'region' => 'Oriental', 'lat' => 34.6814, 'lng' => -1.9086],
            ['code' => 'TET', 'name' => 'Tetouan', 'slug' => 'tetouan', 'region' => 'Tanger-Tetouan-Al Hoceima', 'lat' => 35.5785, 'lng' => -5.3684],
        ];

        $cities = [];

        foreach ($definitions as $definition) {
            $cities[$definition['code']] = City::query()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'name' => $definition['name'],
                    'slug' => $definition['slug'],
                    'country_code' => 'MA',
                    'region' => $definition['region'],
                    'latitude' => $definition['lat'],
                    'longitude' => $definition['lng'],
                    'description' => $definition['name'].' is part of the full tournament host structure.',
                    'status' => 'active',
                ]
            );
        }

        return $cities;
    }

    private function seedStadiums(array $cities): array
    {
        $definitions = [
            ['code' => 'ST-CAS', 'city' => 'CAS', 'name' => 'Mohammed V Stadium', 'slug' => 'mohammed-v-stadium', 'capacity' => 67000],
            ['code' => 'ST-RAB', 'city' => 'RAB', 'name' => 'Prince Moulay Abdellah Stadium', 'slug' => 'prince-moulay-abdellah-stadium', 'capacity' => 69000],
            ['code' => 'ST-TNG', 'city' => 'TNG', 'name' => 'Grand Stade de Tanger', 'slug' => 'grand-stade-de-tanger', 'capacity' => 65000],
            ['code' => 'ST-MRK', 'city' => 'MRK', 'name' => 'Marrakech Stadium', 'slug' => 'marrakech-stadium', 'capacity' => 45000],
            ['code' => 'ST-AGD', 'city' => 'AGD', 'name' => 'Adrar Stadium', 'slug' => 'adrar-stadium', 'capacity' => 45000],
            ['code' => 'ST-FES', 'city' => 'FES', 'name' => 'Fes Stadium', 'slug' => 'fes-stadium', 'capacity' => 35000],
            ['code' => 'ST-OUJ', 'city' => 'OUJ', 'name' => 'Oujda Stadium', 'slug' => 'oujda-stadium', 'capacity' => 45000],
            ['code' => 'ST-TET', 'city' => 'TET', 'name' => 'Tetouan Stadium', 'slug' => 'tetouan-stadium', 'capacity' => 42000],
        ];

        $stadiums = [];

        foreach ($definitions as $definition) {
            $city = $cities[$definition['city']];

            $stadiums[] = Stadium::query()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'city_id' => $city->id,
                    'name' => $definition['name'],
                    'slug' => $definition['slug'],
                    'capacity' => $definition['capacity'],
                    'address' => $city->name.', Morocco',
                    'surface_type' => 'grass',
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'status' => 'active',
                ]
            );
        }

        return $stadiums;
    }

    private function seedGroups(): array
    {
        $groups = [];

        foreach (range('A', 'L') as $index => $code) {
            $groups[$code] = Group::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => 'Group '.$code,
                    'description' => 'Full tournament group '.$code.' with four national teams.',
                    'sort_order' => $index + 1,
                ]
            );
        }

        return $groups;
    }

    private function seedTeams(array $groups): array
    {
        $definitions = [
            'A' => [['Morocco', 'MOR'], ['Spain', 'ESP'], ['Japan', 'JPN'], ['Mexico', 'MEX']],
            'B' => [['France', 'FRA'], ['Senegal', 'SEN'], ['United States', 'USA'], ['South Korea', 'KOR']],
            'C' => [['Brazil', 'BRA'], ['Croatia', 'CRO'], ['Ghana', 'GHA'], ['Australia', 'AUS']],
            'D' => [['Argentina', 'ARG'], ['Portugal', 'POR'], ['England', 'ENG'], ['Germany', 'GER']],
            'E' => [['Netherlands', 'NED'], ['Uruguay', 'URU'], ['Egypt', 'EGY'], ['Canada', 'CAN']],
            'F' => [['Italy', 'ITA'], ['Belgium', 'BEL'], ['Nigeria', 'NGA'], ['Saudi Arabia', 'KSA']],
            'G' => [['Colombia', 'COL'], ['Switzerland', 'SUI'], ['Cameroon', 'CMR'], ['Qatar', 'QAT']],
            'H' => [['Denmark', 'DEN'], ['Sweden', 'SWE'], ['Chile', 'CHI'], ['New Zealand', 'NZL']],
            'I' => [['Norway', 'NOR'], ['Poland', 'POL'], ['Tunisia', 'TUN'], ['Iran', 'IRN']],
            'J' => [['Serbia', 'SRB'], ['Ukraine', 'UKR'], ['Costa Rica', 'CRC'], ['South Africa', 'RSA']],
            'K' => [['Austria', 'AUT'], ['Turkey', 'TUR'], ['Algeria', 'ALG'], ['Jamaica', 'JAM']],
            'L' => [['Wales', 'WAL'], ['Paraguay', 'PAR'], ['Mali', 'MLI'], ['China', 'CHN']],
        ];

        $teams = [];

        foreach ($definitions as $groupCode => $groupTeams) {
            foreach ($groupTeams as [$name, $code]) {
                $teams[$code] = Team::query()->updateOrCreate(
                    ['code' => $code],
                    [
                        'group_id' => $groups[$groupCode]->id,
                        'name' => $name,
                        'short_name' => $code,
                        'slug' => Str::slug($name),
                        'federation_name' => $name.' Football Federation',
                        'coach_name' => null,
                        'team_type' => 'national',
                        'status' => 'active',
                        'meta' => [
                            'full_tournament_structure' => true,
                            'group_code' => $groupCode,
                        ],
                    ]
                );
            }
        }

        return $teams;
    }

    private function seedGroupMatches(array $groups, array $teams, array $cities, array $stadiums): array
    {
        $matches = [];
        $matchIndex = 0;

        foreach ($groups as $groupCode => $group) {
            $groupTeams = Team::query()
                ->where('group_id', $group->id)
                ->orderBy('name')
                ->get()
                ->values();

            for ($homeIndex = 0; $homeIndex < $groupTeams->count(); $homeIndex++) {
                for ($awayIndex = $homeIndex + 1; $awayIndex < $groupTeams->count(); $awayIndex++) {
                    $homeTeam = $groupTeams[$homeIndex];
                    $awayTeam = $groupTeams[$awayIndex];
                    $matchIndex++;
                    $stadium = $stadiums[($matchIndex - 1) % count($stadiums)];
                    $code = sprintf('FT-G%s-%02d', $groupCode, $matchIndexForGroup = $this->groupMatchNumber($matches, $groupCode));

                    $matches[$code] = MatchFixture::query()->updateOrCreate(
                        ['code' => $code],
                        [
                            'stadium_id' => $stadium->id,
                            'city_id' => $stadium->city_id,
                            'group_id' => $group->id,
                            'home_team_id' => $homeTeam->id,
                            'away_team_id' => $awayTeam->id,
                            'slug' => Str::slug($homeTeam->code.'-'.$awayTeam->code.'-group-'.$groupCode),
                            'stage_type' => 'group',
                            'round_number' => $matchIndexForGroup,
                            'match_date' => $this->matchDate(0, $matchIndex),
                            'timezone' => self::TIMEZONE,
                            'status' => 'scheduled',
                            'home_score' => null,
                            'away_score' => null,
                            'home_penalty_score' => null,
                            'away_penalty_score' => null,
                            'extra_time_played' => false,
                            'meta' => ['full_tournament_structure' => true],
                            'published_at' => now(),
                        ]
                    );
                }
            }
        }

        return $matches;
    }

    private function groupMatchNumber(array $matches, string $groupCode): int
    {
        return collect(array_keys($matches))
            ->filter(fn (string $code) => str_starts_with($code, 'FT-G'.$groupCode.'-'))
            ->count() + 1;
    }

    private function seedKnockoutMatches(array $cities, array $stadiums): array
    {
        $rounds = [
            'R32' => ['stage' => 'round_of_32', 'count' => 16, 'day' => 24],
            'R16' => ['stage' => 'round_of_16', 'count' => 8, 'day' => 30],
            'QF' => ['stage' => 'quarter_final', 'count' => 4, 'day' => 35],
            'SF' => ['stage' => 'semi_final', 'count' => 2, 'day' => 40],
        ];

        $matches = [];
        $fixtureIndex = 0;

        foreach ($rounds as $prefix => $round) {
            for ($slot = 1; $slot <= $round['count']; $slot++) {
                $fixtureIndex++;
                $stadium = $stadiums[($fixtureIndex - 1) % count($stadiums)];
                $code = sprintf('FT-%s-%02d', $prefix, $slot);

                $matches[$code] = MatchFixture::query()->updateOrCreate(
                    ['code' => $code],
                    [
                        'stadium_id' => $stadium->id,
                        'city_id' => $stadium->city_id,
                        'group_id' => null,
                        'home_team_id' => null,
                        'away_team_id' => null,
                        'slug' => Str::slug($code),
                        'stage_type' => $round['stage'],
                        'round_number' => $slot,
                        'match_date' => $this->matchDate($round['day'], $slot),
                        'timezone' => self::TIMEZONE,
                        'status' => 'scheduled',
                        'home_score' => null,
                        'away_score' => null,
                        'home_penalty_score' => null,
                        'away_penalty_score' => null,
                        'extra_time_played' => false,
                        'meta' => ['full_tournament_structure' => true],
                        'published_at' => now(),
                    ]
                );
            }
        }

        foreach ([
            'FT-THIRD' => ['stage' => 'third_place', 'day' => 45, 'stadium' => 1],
            'FT-FINAL' => ['stage' => 'final', 'day' => 46, 'stadium' => 0],
        ] as $code => $definition) {
            $stadium = $stadiums[$definition['stadium']];

            $matches[$code] = MatchFixture::query()->updateOrCreate(
                ['code' => $code],
                [
                    'stadium_id' => $stadium->id,
                    'city_id' => $stadium->city_id,
                    'group_id' => null,
                    'home_team_id' => null,
                    'away_team_id' => null,
                    'slug' => Str::slug($code),
                    'stage_type' => $definition['stage'],
                    'round_number' => 1,
                    'match_date' => $this->matchDate($definition['day'], 1),
                    'timezone' => self::TIMEZONE,
                    'status' => 'scheduled',
                    'home_score' => null,
                    'away_score' => null,
                    'home_penalty_score' => null,
                    'away_penalty_score' => null,
                    'extra_time_played' => false,
                    'meta' => ['full_tournament_structure' => true],
                    'published_at' => now(),
                ]
            );
        }

        return $matches;
    }

    private function seedGroupQualificationRules(array $groups, array $matches): void
    {
        $definitions = [
            ['group' => 'A', 'position' => 1, 'target' => 'FT-R32-01', 'slot' => 'home'],
            ['group' => 'B', 'position' => 2, 'target' => 'FT-R32-01', 'slot' => 'away'],
            ['group' => 'C', 'position' => 1, 'target' => 'FT-R32-02', 'slot' => 'home'],
            ['group' => 'D', 'position' => 2, 'target' => 'FT-R32-02', 'slot' => 'away'],
            ['group' => 'E', 'position' => 1, 'target' => 'FT-R32-03', 'slot' => 'home'],
            ['group' => 'F', 'position' => 2, 'target' => 'FT-R32-03', 'slot' => 'away'],
            ['group' => 'G', 'position' => 1, 'target' => 'FT-R32-04', 'slot' => 'home'],
            ['group' => 'H', 'position' => 2, 'target' => 'FT-R32-04', 'slot' => 'away'],
            ['group' => 'I', 'position' => 1, 'target' => 'FT-R32-05', 'slot' => 'home'],
            ['group' => 'J', 'position' => 2, 'target' => 'FT-R32-05', 'slot' => 'away'],
            ['group' => 'K', 'position' => 1, 'target' => 'FT-R32-06', 'slot' => 'home'],
            ['group' => 'L', 'position' => 2, 'target' => 'FT-R32-06', 'slot' => 'away'],
            ['group' => 'B', 'position' => 1, 'target' => 'FT-R32-07', 'slot' => 'home'],
            ['group' => 'A', 'position' => 2, 'target' => 'FT-R32-07', 'slot' => 'away'],
            ['group' => 'D', 'position' => 1, 'target' => 'FT-R32-08', 'slot' => 'home'],
            ['group' => 'C', 'position' => 2, 'target' => 'FT-R32-08', 'slot' => 'away'],
            ['group' => 'F', 'position' => 1, 'target' => 'FT-R32-09', 'slot' => 'home'],
            ['group' => 'E', 'position' => 2, 'target' => 'FT-R32-09', 'slot' => 'away'],
            ['group' => 'H', 'position' => 1, 'target' => 'FT-R32-10', 'slot' => 'home'],
            ['group' => 'G', 'position' => 2, 'target' => 'FT-R32-10', 'slot' => 'away'],
            ['group' => 'J', 'position' => 1, 'target' => 'FT-R32-11', 'slot' => 'home'],
            ['group' => 'I', 'position' => 2, 'target' => 'FT-R32-11', 'slot' => 'away'],
            ['group' => 'L', 'position' => 1, 'target' => 'FT-R32-12', 'slot' => 'home'],
            ['group' => 'K', 'position' => 2, 'target' => 'FT-R32-12', 'slot' => 'away'],
            ['group' => 'A', 'position' => 3, 'target' => 'FT-R32-13', 'slot' => 'home'],
            ['group' => 'B', 'position' => 3, 'target' => 'FT-R32-13', 'slot' => 'away'],
            ['group' => 'C', 'position' => 3, 'target' => 'FT-R32-14', 'slot' => 'home'],
            ['group' => 'D', 'position' => 3, 'target' => 'FT-R32-14', 'slot' => 'away'],
            ['group' => 'E', 'position' => 3, 'target' => 'FT-R32-15', 'slot' => 'home'],
            ['group' => 'F', 'position' => 3, 'target' => 'FT-R32-15', 'slot' => 'away'],
            ['group' => 'G', 'position' => 3, 'target' => 'FT-R32-16', 'slot' => 'home'],
            ['group' => 'H', 'position' => 3, 'target' => 'FT-R32-16', 'slot' => 'away'],
        ];

        foreach ($definitions as $definition) {
            GroupQualificationRule::query()->updateOrCreate(
                [
                    'group_id' => $groups[$definition['group']]->id,
                    'qualifying_position' => $definition['position'],
                    'target_match_id' => $matches[$definition['target']]->id,
                    'team_slot' => $definition['slot'],
                ],
                [
                    'label' => $this->qualificationLabel($definition['position'], $definition['group']),
                    'notes' => 'Full tournament group qualification mapping.',
                ]
            );
        }
    }

    private function seedInitialStandings(array $groups): void
    {
        foreach ($groups as $group) {
            Standing::query()->where('group_id', $group->id)->delete();

            $teams = Team::query()
                ->where('group_id', $group->id)
                ->orderBy('name')
                ->get();

            foreach ($teams as $index => $team) {
                Standing::query()->create([
                    'group_id' => $group->id,
                    'team_id' => $team->id,
                    'position' => $index + 1,
                    'played' => 0,
                    'won' => 0,
                    'drawn' => 0,
                    'lost' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                    'points' => 0,
                    'form' => null,
                    'fair_play_points' => 0,
                    'meta' => [
                        'source' => 'full-tournament-structure-seeder',
                        'generated_at' => now()->toIso8601String(),
                    ],
                ]);
            }
        }
    }

    private function seedKnockoutProgressions(array $matches): void
    {
        $definitions = [];

        foreach (range(1, 16) as $slot) {
            $definitions[] = [
                'source' => sprintf('FT-R32-%02d', $slot),
                'target' => sprintf('FT-R16-%02d', (int) ceil($slot / 2)),
                'slot' => $slot % 2 === 1 ? 'home' : 'away',
            ];
        }

        foreach (range(1, 8) as $slot) {
            $definitions[] = [
                'source' => sprintf('FT-R16-%02d', $slot),
                'target' => sprintf('FT-QF-%02d', (int) ceil($slot / 2)),
                'slot' => $slot % 2 === 1 ? 'home' : 'away',
            ];
        }

        foreach (range(1, 4) as $slot) {
            $definitions[] = [
                'source' => sprintf('FT-QF-%02d', $slot),
                'target' => sprintf('FT-SF-%02d', (int) ceil($slot / 2)),
                'slot' => $slot % 2 === 1 ? 'home' : 'away',
            ];
        }

        $definitions = [
            ...$definitions,
            ['source' => 'FT-SF-01', 'target' => 'FT-FINAL', 'slot' => 'home', 'type' => 'winner'],
            ['source' => 'FT-SF-02', 'target' => 'FT-FINAL', 'slot' => 'away', 'type' => 'winner'],
            ['source' => 'FT-SF-01', 'target' => 'FT-THIRD', 'slot' => 'home', 'type' => 'loser'],
            ['source' => 'FT-SF-02', 'target' => 'FT-THIRD', 'slot' => 'away', 'type' => 'loser'],
        ];

        foreach ($definitions as $definition) {
            KnockoutProgression::query()->updateOrCreate(
                [
                    'source_match_id' => $matches[$definition['source']]->id,
                    'target_match_id' => $matches[$definition['target']]->id,
                    'progression_type' => $definition['type'] ?? 'winner',
                    'team_slot' => $definition['slot'],
                ],
                ['notes' => 'Full tournament knockout dependency mapping.']
            );
        }
    }

    private function qualificationLabel(int $position, string $groupCode): string
    {
        return match ($position) {
            1 => 'Winner of Group '.$groupCode,
            2 => 'Runner-up of Group '.$groupCode,
            3 => 'Third place of Group '.$groupCode,
            default => 'Qualifier '.$position.' of Group '.$groupCode,
        };
    }

    private function matchDate(int $dayOffset, int $slot): CarbonImmutable
    {
        return CarbonImmutable::create(2030, 6, 13, 18, 0, 0, self::TIMEZONE)
            ->addDays($dayOffset)
            ->addHours(($slot - 1) % 3 * 3);
    }
}
