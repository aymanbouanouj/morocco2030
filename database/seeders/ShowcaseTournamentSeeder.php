<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\City;
use App\Models\EditorialWorkflow;
use App\Models\Group;
use App\Models\KnockoutProgression;
use App\Models\Language;
use App\Models\MatchEvent;
use App\Models\MatchFixture;
use App\Models\MatchLineup;
use App\Models\MatchStatistic;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Role;
use App\Models\Stadium;
use App\Models\Team;
use App\Models\Translation;
use App\Models\User;
use App\Services\Sports\KnockoutProgressionService;
use App\Services\Sports\StandingsRecalculationService;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShowcaseTournamentSeeder extends Seeder
{
    private const TIMEZONE = 'Africa/Casablanca';

    private Language $english;

    public function run(): void
    {
        $this->english = Language::query()->where('code', 'en')->firstOrFail();

        $users = $this->seedUsers();
        $categories = $this->seedNewsCategories();
        $groups = $this->seedGroups();
        $cities = $this->seedCities();
        $stadiums = $this->seedStadiums($cities);
        [$teams, $players] = $this->seedTeamsAndPlayers($groups);

        $this->seedPartners();

        $news = $this->seedNews($categories, $users);
        $matches = $this->seedMatches($groups, $teams, $cities, $stadiums);

        $this->seedMatchEvents($matches, $teams, $players);
        $this->seedMatchStatistics($matches, $teams);
        $this->seedMatchLineups($matches, $teams, $players);
        $this->seedKnockoutProgressions($matches);

        foreach ($groups as $group) {
            app(StandingsRecalculationService::class)->recalculateGroup($group->fresh('teams'));
        }

        foreach (['M2030-QF1', 'M2030-QF2'] as $completedKnockoutCode) {
            app(KnockoutProgressionService::class)->propagateCascade($matches[$completedKnockoutCode]->fresh('sourceProgressions.targetMatch'));
        }

        $this->seedEditorialWorkflows($news, $users);
        $this->seedAuditLogs($users, $matches, $news, $groups);
    }

    private function seedUsers(): array
    {
        $roles = Role::query()
            ->whereIn('slug', ['super-admin', 'chief-editor', 'journalist', 'competition-manager'])
            ->get()
            ->keyBy('slug');

        $definitions = [
            [
                'role' => 'super-admin',
                'name' => 'Amina El Idrissi',
                'email' => 'amina.elidrissi@morocco2030-showcase.test',
                'preferred_locale' => 'en',
            ],
            [
                'role' => 'chief-editor',
                'name' => 'Sara Bennani',
                'email' => 'sara.bennani@morocco2030-showcase.test',
                'preferred_locale' => 'en',
            ],
            [
                'role' => 'journalist',
                'name' => 'Youssef Benali',
                'email' => 'youssef.benali@morocco2030-showcase.test',
                'preferred_locale' => 'en',
            ],
            [
                'role' => 'competition-manager',
                'name' => 'Karim Alaoui',
                'email' => 'karim.alaoui@morocco2030-showcase.test',
                'preferred_locale' => 'en',
            ],
        ];

        $users = [];

        foreach ($definitions as $definition) {
            $user = User::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'user_type' => 'staff',
                    'status' => 'active',
                    'preferred_locale' => $definition['preferred_locale'],
                    'password' => Str::random(40),
                ]
            );

            $role = $roles[$definition['role']] ?? null;

            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            $users[$definition['role']] = $user;
        }

        return $users;
    }

    private function seedNewsCategories(): array
    {
        $definitions = [
            [
                'name' => 'Tournament Updates',
                'slug' => 'tournament-updates',
                'description' => 'Official competition-wide operational and sporting updates.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Host Cities',
                'slug' => 'host-cities',
                'description' => 'Stories focused on venues, transport, and destination readiness.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Matchday Briefings',
                'slug' => 'matchday-briefings',
                'description' => 'Editorial coverage surrounding the current match window.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Partners',
                'slug' => 'partners',
                'description' => 'Official partner and sponsor programme updates.',
                'sort_order' => 4,
            ],
        ];

        $categories = [];

        foreach ($definitions as $definition) {
            $categories[$definition['slug']] = NewsCategory::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'status' => 'active',
                    'sort_order' => $definition['sort_order'],
                ]
            );
        }

        return $categories;
    }

    private function seedGroups(): array
    {
        $definitions = [
            'A' => 'Group A sets the early rhythm of the showcase competition with the host nation positioned in the opening match window.',
            'B' => 'Group B combines transitional power and compact defensive structures across four experienced national teams.',
            'C' => 'Group C brings technical control and direct attacking quality into one of the most balanced tables in the demo schedule.',
            'D' => 'Group D closes the first phase with heavyweight fixtures and a high-pressure qualification race.',
        ];

        $groups = [];
        $sortOrder = 1;

        foreach ($definitions as $code => $description) {
            $groups[$code] = Group::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => 'Group '.$code,
                    'description' => $description,
                    'sort_order' => $sortOrder++,
                ]
            );
        }

        return $groups;
    }

    private function seedCities(): array
    {
        $definitions = [
            [
                'key' => 'casablanca',
                'name' => 'Casablanca',
                'slug' => 'casablanca',
                'code' => 'CAS',
                'region' => 'Casablanca-Settat',
                'latitude' => 33.5731,
                'longitude' => -7.5898,
                'description' => 'Casablanca anchors the showcase match calendar with a high-capacity venue programme, hospitality corridors, and central transport distribution across the coast.',
            ],
            [
                'key' => 'rabat',
                'name' => 'Rabat',
                'slug' => 'rabat',
                'code' => 'RAB',
                'region' => 'Rabat-Sale-Kenitra',
                'latitude' => 34.0209,
                'longitude' => -6.8416,
                'description' => 'Rabat combines institutional ceremony, secure mobility planning, and a refined operations footprint for premium tournament windows.',
            ],
            [
                'key' => 'tangier',
                'name' => 'Tangier',
                'slug' => 'tangier',
                'code' => 'TNG',
                'region' => 'Tanger-Tetouan-Al Hoceima',
                'latitude' => 35.7595,
                'longitude' => -5.8340,
                'description' => 'Tangier gives the showcase schedule a northern gateway venue with strong port access, hospitality stock, and cross-city fan movement planning.',
            ],
            [
                'key' => 'marrakech',
                'name' => 'Marrakech',
                'slug' => 'marrakech',
                'code' => 'MRK',
                'region' => 'Marrakech-Safi',
                'latitude' => 31.6295,
                'longitude' => -7.9811,
                'description' => 'Marrakech pairs destination appeal with tournament-day logistics, supporting evening fixtures and international visitor flows with a hospitality-led profile.',
            ],
            [
                'key' => 'agadir',
                'name' => 'Agadir',
                'slug' => 'agadir',
                'code' => 'AGD',
                'region' => 'Souss-Massa',
                'latitude' => 30.4278,
                'longitude' => -9.5981,
                'description' => 'Agadir extends the coastal competition footprint and supports the showcase tournament with modern venue circulation and strong weather resilience planning.',
            ],
            [
                'key' => 'fes',
                'name' => 'Fes',
                'slug' => 'fes',
                'code' => 'FES',
                'region' => 'Fes-Meknes',
                'latitude' => 34.0181,
                'longitude' => -5.0078,
                'description' => 'Fes strengthens the host network with an interior-city venue cluster suited to balanced match scheduling and controlled supporter access routes.',
            ],
        ];

        $cities = [];

        foreach ($definitions as $definition) {
            $cities[$definition['key']] = City::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'code' => $definition['code'],
                    'country_code' => 'MA',
                    'region' => $definition['region'],
                    'latitude' => $definition['latitude'],
                    'longitude' => $definition['longitude'],
                    'description' => $definition['description'],
                    'status' => 'active',
                ]
            );
        }

        return $cities;
    }

    private function seedStadiums(array $cities): array
    {
        $definitions = [
            [
                'key' => 'mohammed-v',
                'city' => 'casablanca',
                'name' => 'Mohammed V Stadium',
                'slug' => 'mohammed-v-stadium',
                'code' => 'ST-CAS',
                'capacity' => 67000,
                'opened_year' => 1955,
                'address' => 'Boulevard de Fes, Casablanca',
                'surface_type' => 'grass',
                'latitude' => 33.5829,
                'longitude' => -7.6520,
                'description' => 'Mohammed V Stadium sits at the centre of the showcase tournament narrative, pairing ceremonial weight with a strong matchday arrival and hospitality envelope.',
            ],
            [
                'key' => 'prince-moulay-abdellah',
                'city' => 'rabat',
                'name' => 'Prince Moulay Abdellah Stadium',
                'slug' => 'prince-moulay-abdellah-stadium',
                'code' => 'ST-RAB',
                'capacity' => 69000,
                'opened_year' => 1983,
                'address' => 'Avenue Annakhil, Rabat',
                'surface_type' => 'grass',
                'latitude' => 33.9569,
                'longitude' => -6.8529,
                'description' => 'Prince Moulay Abdellah Stadium is configured as a premium host venue for live match windows, senior delegations, and late-round competition activity.',
            ],
            [
                'key' => 'grand-stade-de-tanger',
                'city' => 'tangier',
                'name' => 'Grand Stade de Tanger',
                'slug' => 'grand-stade-de-tanger',
                'code' => 'ST-TNG',
                'capacity' => 65000,
                'opened_year' => 2011,
                'address' => 'Route de Rabat, Tangier',
                'surface_type' => 'grass',
                'latitude' => 35.7245,
                'longitude' => -5.8386,
                'description' => 'Grand Stade de Tanger gives the showcase calendar a major northern tournament arena with clean mobility corridors and broad spectator circulation zones.',
            ],
            [
                'key' => 'marrakech-stadium',
                'city' => 'marrakech',
                'name' => 'Marrakech Stadium',
                'slug' => 'marrakech-stadium',
                'code' => 'ST-MRK',
                'capacity' => 45000,
                'opened_year' => 2011,
                'address' => 'Route de Casablanca, Marrakech',
                'surface_type' => 'grass',
                'latitude' => 31.6936,
                'longitude' => -8.0340,
                'description' => 'Marrakech Stadium supports showcase night fixtures with a balanced venue bowl, strong hospitality mix, and efficient city-to-venue transfer routes.',
            ],
            [
                'key' => 'adrar-stadium',
                'city' => 'agadir',
                'name' => 'Adrar Stadium',
                'slug' => 'adrar-stadium',
                'code' => 'ST-AGD',
                'capacity' => 45000,
                'opened_year' => 2013,
                'address' => 'Adrar District, Agadir',
                'surface_type' => 'grass',
                'latitude' => 30.4072,
                'longitude' => -9.5525,
                'description' => 'Adrar Stadium strengthens the southern tournament circuit with controlled match operations, modern support spaces, and a compact urban arrival pattern.',
            ],
            [
                'key' => 'fes-stadium',
                'city' => 'fes',
                'name' => 'Fes Stadium',
                'slug' => 'fes-stadium',
                'code' => 'ST-FES',
                'capacity' => 35000,
                'opened_year' => 2007,
                'address' => 'Ain Chkef, Fes',
                'surface_type' => 'grass',
                'latitude' => 33.9879,
                'longitude' => -4.9774,
                'description' => 'Fes Stadium extends the showcase host programme with a clean operational footprint suited to group-stage sequencing and regional supporter movement.',
            ],
        ];

        $stadiums = [];

        foreach ($definitions as $definition) {
            $stadium = Stadium::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'city_id' => $cities[$definition['city']]->id,
                    'name' => $definition['name'],
                    'code' => $definition['code'],
                    'capacity' => $definition['capacity'],
                    'address' => $definition['address'],
                    'latitude' => $definition['latitude'],
                    'longitude' => $definition['longitude'],
                    'opened_year' => $definition['opened_year'],
                    'surface_type' => $definition['surface_type'],
                    'status' => 'active',
                ]
            );

            $this->syncTranslation($stadium, 'description', $definition['description']);

            $stadiums[$definition['key']] = $stadium;
        }

        return $stadiums;
    }

    private function seedTeamsAndPlayers(array $groups): array
    {
        $definitions = [
            'A' => [
                [
                    'name' => 'Morocco',
                    'code' => 'MOR',
                    'coach' => 'Walid Regragui',
                    'federation' => 'Royal Moroccan Football Federation',
                    'description' => 'Morocco leads the showcase host narrative with quick transitional attacks, disciplined structure, and a high-energy crowd connection.',
                    'country_code' => 'MA',
                    'players' => [
                        ['Yassine Bounou', 'goalkeeper', 1, 'Al Hilal'],
                        ['Achraf Hakimi', 'defender', 2, 'Paris Saint-Germain'],
                        ['Nayef Aguerd', 'defender', 5, 'West Ham United'],
                        ['Sofyan Amrabat', 'midfielder', 4, 'Fenerbahce'],
                        ['Azzedine Ounahi', 'midfielder', 8, 'Panathinaikos'],
                        ['Hakim Ziyech', 'forward', 7, 'Galatasaray'],
                        ['Youssef En-Nesyri', 'forward', 9, 'Sevilla'],
                    ],
                ],
                [
                    'name' => 'Spain',
                    'code' => 'ESP',
                    'coach' => 'Luis de la Fuente',
                    'federation' => 'Royal Spanish Football Federation',
                    'description' => 'Spain brings circulation control, technical midfield dominance, and wide attacking movement into the showcase group phase.',
                    'country_code' => 'ES',
                    'players' => [
                        ['Unai Simon', 'goalkeeper', 1, 'Athletic Club'],
                        ['Dani Carvajal', 'defender', 2, 'Real Madrid'],
                        ['Aymeric Laporte', 'defender', 14, 'Al Nassr'],
                        ['Rodri Hernandez', 'midfielder', 16, 'Manchester City'],
                        ['Pedri Gonzalez', 'midfielder', 20, 'Barcelona'],
                        ['Nico Williams', 'forward', 11, 'Athletic Club'],
                        ['Alvaro Morata', 'forward', 7, 'Atletico Madrid'],
                    ],
                ],
                [
                    'name' => 'Mexico',
                    'code' => 'MEX',
                    'coach' => 'Jaime Lozano',
                    'federation' => 'Mexican Football Federation',
                    'description' => 'Mexico is seeded as an aggressive pressing side with experienced defensive leadership and quick vertical outlets.',
                    'country_code' => 'MX',
                    'players' => [
                        ['Guillermo Ochoa', 'goalkeeper', 13, 'Salernitana'],
                        ['Cesar Montes', 'defender', 3, 'Almeria'],
                        ['Johan Vasquez', 'defender', 5, 'Genoa'],
                        ['Edson Alvarez', 'midfielder', 4, 'West Ham United'],
                        ['Luis Chavez', 'midfielder', 18, 'Dynamo Moscow'],
                        ['Hirving Lozano', 'forward', 11, 'PSV Eindhoven'],
                        ['Santiago Gimenez', 'forward', 9, 'Feyenoord'],
                    ],
                ],
                [
                    'name' => 'Japan',
                    'code' => 'JPN',
                    'coach' => 'Hajime Moriyasu',
                    'federation' => 'Japan Football Association',
                    'description' => 'Japan gives Group A tactical flexibility, fast rest defence, and a disciplined collective structure in possession and transition.',
                    'country_code' => 'JP',
                    'players' => [
                        ['Zion Suzuki', 'goalkeeper', 23, 'Parma'],
                        ['Takehiro Tomiyasu', 'defender', 22, 'Arsenal'],
                        ['Ko Itakura', 'defender', 4, 'Borussia Monchengladbach'],
                        ['Wataru Endo', 'midfielder', 6, 'Liverpool'],
                        ['Daichi Kamada', 'midfielder', 15, 'Lazio'],
                        ['Kaoru Mitoma', 'forward', 7, 'Brighton & Hove Albion'],
                        ['Takuma Asano', 'forward', 18, 'Bochum'],
                    ],
                ],
            ],
            'B' => [
                [
                    'name' => 'France',
                    'code' => 'FRA',
                    'coach' => 'Didier Deschamps',
                    'federation' => 'French Football Federation',
                    'description' => 'France combines high-level athleticism, midfield power, and elite finishing depth across the showcase knockout picture.',
                    'country_code' => 'FR',
                    'players' => [
                        ['Mike Maignan', 'goalkeeper', 16, 'AC Milan'],
                        ['Jules Kounde', 'defender', 5, 'Barcelona'],
                        ['William Saliba', 'defender', 17, 'Arsenal'],
                        ['Aurelien Tchouameni', 'midfielder', 8, 'Real Madrid'],
                        ['Adrien Rabiot', 'midfielder', 14, 'Juventus'],
                        ['Antoine Griezmann', 'forward', 7, 'Atletico Madrid'],
                        ['Kylian Mbappe', 'forward', 10, 'Paris Saint-Germain'],
                    ],
                ],
                [
                    'name' => 'Senegal',
                    'code' => 'SEN',
                    'coach' => 'Aliou Cisse',
                    'federation' => 'Senegalese Football Federation',
                    'description' => 'Senegal enters the showcase bracket with defensive authority, wide running power, and a direct attacking profile.',
                    'country_code' => 'SN',
                    'players' => [
                        ['Edouard Mendy', 'goalkeeper', 16, 'Al Ahli'],
                        ['Kalidou Koulibaly', 'defender', 3, 'Al Hilal'],
                        ['Abdou Diallo', 'defender', 22, 'Al Arabi'],
                        ['Nampalys Mendy', 'midfielder', 6, 'Lens'],
                        ['Pape Matar Sarr', 'midfielder', 17, 'Tottenham Hotspur'],
                        ['Ismaila Sarr', 'forward', 18, 'Marseille'],
                        ['Sadio Mane', 'forward', 10, 'Al Nassr'],
                    ],
                ],
                [
                    'name' => 'United States',
                    'code' => 'USA',
                    'coach' => 'Gregg Berhalter',
                    'federation' => 'U.S. Soccer Federation',
                    'description' => 'The United States side is shaped around intensity, athletic pressing, and midfield coverage across the first phase.',
                    'country_code' => 'US',
                    'players' => [
                        ['Matt Turner', 'goalkeeper', 1, 'Nottingham Forest'],
                        ['Chris Richards', 'defender', 3, 'Crystal Palace'],
                        ['Antonee Robinson', 'defender', 5, 'Fulham'],
                        ['Tyler Adams', 'midfielder', 4, 'Bournemouth'],
                        ['Weston McKennie', 'midfielder', 8, 'Juventus'],
                        ['Christian Pulisic', 'forward', 10, 'AC Milan'],
                        ['Folarin Balogun', 'forward', 20, 'Monaco'],
                    ],
                ],
                [
                    'name' => 'Netherlands',
                    'code' => 'NED',
                    'coach' => 'Ronald Koeman',
                    'federation' => 'Royal Dutch Football Association',
                    'description' => 'The Netherlands gives Group B ball progression quality, aerial security, and efficient final-third movement.',
                    'country_code' => 'NL',
                    'players' => [
                        ['Bart Verbruggen', 'goalkeeper', 1, 'Brighton & Hove Albion'],
                        ['Virgil van Dijk', 'defender', 4, 'Liverpool'],
                        ['Nathan Ake', 'defender', 5, 'Manchester City'],
                        ['Frenkie de Jong', 'midfielder', 21, 'Barcelona'],
                        ['Tijjani Reijnders', 'midfielder', 14, 'AC Milan'],
                        ['Cody Gakpo', 'forward', 11, 'Liverpool'],
                        ['Memphis Depay', 'forward', 10, 'Atletico Madrid'],
                    ],
                ],
            ],
            'C' => [
                [
                    'name' => 'Brazil',
                    'code' => 'BRA',
                    'coach' => 'Dorival Junior',
                    'federation' => 'Brazilian Football Confederation',
                    'description' => 'Brazil supplies rhythm, one-versus-one quality, and decisive attacking speed throughout the showcase sports layer.',
                    'country_code' => 'BR',
                    'players' => [
                        ['Alisson Becker', 'goalkeeper', 1, 'Liverpool'],
                        ['Marquinhos', 'defender', 4, 'Paris Saint-Germain'],
                        ['Gabriel Magalhaes', 'defender', 14, 'Arsenal'],
                        ['Bruno Guimaraes', 'midfielder', 5, 'Newcastle United'],
                        ['Lucas Paqueta', 'midfielder', 7, 'West Ham United'],
                        ['Vinicius Junior', 'forward', 10, 'Real Madrid'],
                        ['Rodrygo Goes', 'forward', 11, 'Real Madrid'],
                    ],
                ],
                [
                    'name' => 'Croatia',
                    'code' => 'CRO',
                    'coach' => 'Zlatko Dalic',
                    'federation' => 'Croatian Football Federation',
                    'description' => 'Croatia balances deep tournament experience, strong midfield control, and measured decision-making under pressure.',
                    'country_code' => 'HR',
                    'players' => [
                        ['Dominik Livakovic', 'goalkeeper', 1, 'Fenerbahce'],
                        ['Josko Gvardiol', 'defender', 4, 'Manchester City'],
                        ['Josip Sutalo', 'defender', 6, 'Ajax'],
                        ['Luka Modric', 'midfielder', 10, 'Real Madrid'],
                        ['Marcelo Brozovic', 'midfielder', 11, 'Al Nassr'],
                        ['Mateo Kovacic', 'midfielder', 8, 'Manchester City'],
                        ['Andrej Kramaric', 'forward', 9, 'Hoffenheim'],
                    ],
                ],
                [
                    'name' => 'South Korea',
                    'code' => 'KOR',
                    'coach' => 'Jurgen Klinsmann',
                    'federation' => 'Korea Football Association',
                    'description' => 'South Korea contributes compact structure, disciplined build-up lanes, and strong attacking leaders in transition moments.',
                    'country_code' => 'KR',
                    'players' => [
                        ['Jo Hyeon-woo', 'goalkeeper', 21, 'Ulsan HD'],
                        ['Kim Min-jae', 'defender', 4, 'Bayern Munich'],
                        ['Kim Young-gwon', 'defender', 19, 'Ulsan HD'],
                        ['Hwang In-beom', 'midfielder', 6, 'Red Star Belgrade'],
                        ['Lee Kang-in', 'midfielder', 18, 'Paris Saint-Germain'],
                        ['Son Heung-min', 'forward', 7, 'Tottenham Hotspur'],
                        ['Cho Gue-sung', 'forward', 9, 'Midtjylland'],
                    ],
                ],
                [
                    'name' => 'Belgium',
                    'code' => 'BEL',
                    'coach' => 'Domenico Tedesco',
                    'federation' => 'Royal Belgian Football Association',
                    'description' => 'Belgium gives the showcase tournament an experienced attacking group supported by vertical midfield running and wide dribbling quality.',
                    'country_code' => 'BE',
                    'players' => [
                        ['Koen Casteels', 'goalkeeper', 1, 'Wolfsburg'],
                        ['Jan Vertonghen', 'defender', 5, 'Anderlecht'],
                        ['Wout Faes', 'defender', 4, 'Leicester City'],
                        ['Amadou Onana', 'midfielder', 24, 'Everton'],
                        ['Youri Tielemans', 'midfielder', 8, 'Aston Villa'],
                        ['Jeremy Doku', 'forward', 22, 'Manchester City'],
                        ['Romelu Lukaku', 'forward', 10, 'Roma'],
                    ],
                ],
            ],
            'D' => [
                [
                    'name' => 'Argentina',
                    'code' => 'ARG',
                    'coach' => 'Lionel Scaloni',
                    'federation' => 'Argentine Football Association',
                    'description' => 'Argentina brings composure, compact spacing, and elite final-third decision-making into the latter stages of the showcase bracket.',
                    'country_code' => 'AR',
                    'players' => [
                        ['Emiliano Martinez', 'goalkeeper', 23, 'Aston Villa'],
                        ['Cristian Romero', 'defender', 13, 'Tottenham Hotspur'],
                        ['Nicolas Otamendi', 'defender', 19, 'Benfica'],
                        ['Enzo Fernandez', 'midfielder', 8, 'Chelsea'],
                        ['Alexis Mac Allister', 'midfielder', 20, 'Liverpool'],
                        ['Lionel Messi', 'forward', 10, 'Inter Miami'],
                        ['Lautaro Martinez', 'forward', 9, 'Inter Milan'],
                    ],
                ],
                [
                    'name' => 'Portugal',
                    'code' => 'POR',
                    'coach' => 'Roberto Martinez',
                    'federation' => 'Portuguese Football Federation',
                    'description' => 'Portugal blends structured build-up, attacking variety, and experienced match management across the premium showcase schedule.',
                    'country_code' => 'PT',
                    'players' => [
                        ['Diogo Costa', 'goalkeeper', 1, 'Porto'],
                        ['Ruben Dias', 'defender', 3, 'Manchester City'],
                        ['Joao Cancelo', 'defender', 20, 'Barcelona'],
                        ['Bernardo Silva', 'midfielder', 10, 'Manchester City'],
                        ['Bruno Fernandes', 'midfielder', 8, 'Manchester United'],
                        ['Rafael Leao', 'forward', 17, 'AC Milan'],
                        ['Cristiano Ronaldo', 'forward', 7, 'Al Nassr'],
                    ],
                ],
                [
                    'name' => 'Germany',
                    'code' => 'GER',
                    'coach' => 'Julian Nagelsmann',
                    'federation' => 'German Football Association',
                    'description' => 'Germany features layered midfield control and direct attacking combinations, even in a tightly contested qualification group.',
                    'country_code' => 'DE',
                    'players' => [
                        ['Marc-Andre ter Stegen', 'goalkeeper', 1, 'Barcelona'],
                        ['Antonio Rudiger', 'defender', 2, 'Real Madrid'],
                        ['Jonathan Tah', 'defender', 4, 'Bayer Leverkusen'],
                        ['Joshua Kimmich', 'midfielder', 6, 'Bayern Munich'],
                        ['Ilkay Gundogan', 'midfielder', 21, 'Barcelona'],
                        ['Jamal Musiala', 'forward', 10, 'Bayern Munich'],
                        ['Kai Havertz', 'forward', 7, 'Arsenal'],
                    ],
                ],
                [
                    'name' => 'England',
                    'code' => 'ENG',
                    'coach' => 'Gareth Southgate',
                    'federation' => 'The Football Association',
                    'description' => 'England contributes wide attacking depth, strong midfield running, and measured control in late-game tournament phases.',
                    'country_code' => 'GB',
                    'players' => [
                        ['Jordan Pickford', 'goalkeeper', 1, 'Everton'],
                        ['John Stones', 'defender', 5, 'Manchester City'],
                        ['Kyle Walker', 'defender', 2, 'Manchester City'],
                        ['Declan Rice', 'midfielder', 4, 'Arsenal'],
                        ['Jude Bellingham', 'midfielder', 10, 'Real Madrid'],
                        ['Phil Foden', 'forward', 11, 'Manchester City'],
                        ['Harry Kane', 'forward', 9, 'Bayern Munich'],
                    ],
                ],
            ],
        ];

        $teams = [];
        $players = [];

        foreach ($definitions as $groupCode => $teamsInGroup) {
            foreach ($teamsInGroup as $teamDefinition) {
                $team = Team::query()->updateOrCreate(
                    ['code' => $teamDefinition['code']],
                    [
                        'group_id' => $groups[$groupCode]->id,
                        'name' => $teamDefinition['name'],
                        'short_name' => $teamDefinition['code'],
                        'slug' => Str::slug($teamDefinition['name']),
                        'federation_name' => $teamDefinition['federation'],
                        'founded_year' => 1950,
                        'coach_name' => $teamDefinition['coach'],
                        'team_type' => 'national',
                        'status' => 'active',
                        'meta' => [
                            'showcase_group' => $groupCode,
                            'source' => 'showcase-seeder',
                        ],
                    ]
                );

                $this->syncTranslation($team, 'description', $teamDefinition['description']);

                $teams[$teamDefinition['code']] = $team;
                $players[$teamDefinition['code']] = $this->seedPlayersForTeam($team, $teamDefinition['players'], $teamDefinition['country_code']);
            }
        }

        return [$teams, $players];
    }

    private function seedPlayersForTeam(Team $team, array $definitions, string $nationalityCode): array
    {
        $players = [];

        foreach ($definitions as $index => [$displayName, $position, $shirtNumber, $club]) {
            $player = Player::query()->updateOrCreate(
                ['slug' => Str::slug($team->code.' '.$displayName)],
                [
                    'team_id' => $team->id,
                    'display_name' => $displayName,
                    'first_name' => Str::of($displayName)->before(' ')->toString(),
                    'last_name' => Str::of($displayName)->after(' ')->toString(),
                    'shirt_number' => $shirtNumber,
                    'position' => $position,
                    'date_of_birth' => now()->subYears(23 + ($index % 8))->subDays($index * 21)->toDateString(),
                    'nationality_code' => $nationalityCode,
                    'height_cm' => 176 + ($index * 2),
                    'weight_kg' => 70 + $index,
                    'bio' => $displayName.' is part of the '.$team->name.' showcase squad and features in the tournament demonstration dataset as a '.$position.' with a leading role in current match operations.',
                    'is_captain' => $index === 0 || ($position === 'forward' && $index === count($definitions) - 1),
                    'status' => 'active',
                ]
            );

            $this->syncTranslation($player, 'club', $club);

            $players[$displayName] = $player;
        }

        return $players;
    }

    private function seedPartners(): void
    {
        $definitions = [
            [
                'name' => 'Royal Air Maroc',
                'slug' => 'royal-air-maroc',
                'category' => 'Mobility Partner',
                'tier' => 'Official Airline Partner',
                'website_url' => 'https://www.royalairmaroc.com',
                'description' => 'Royal Air Maroc supports long-haul tournament mobility, delegation routing, and showcase visitor connectivity across host-city arrival windows.',
            ],
            [
                'name' => 'ONCF',
                'slug' => 'oncf',
                'category' => 'Transport Partner',
                'tier' => 'National Rail Partner',
                'website_url' => 'https://www.oncf.ma',
                'description' => 'ONCF anchors inter-city rail movement in the showcase schedule, supporting predictable supporter, workforce, and media transfers on matchdays.',
            ],
            [
                'name' => 'Maroc Telecom',
                'slug' => 'maroc-telecom',
                'category' => 'Connectivity Partner',
                'tier' => 'Official Technology Partner',
                'website_url' => 'https://www.iam.ma',
                'description' => 'Maroc Telecom provides connectivity support for venue operations, media working areas, and premium public-facing communication layers.',
            ],
            [
                'name' => 'OCP Group',
                'slug' => 'ocp-group',
                'category' => 'Sustainability Partner',
                'tier' => 'Strategic Partner',
                'website_url' => 'https://www.ocpgroup.ma',
                'description' => 'OCP Group appears in the showcase programme as a long-horizon sustainability and infrastructure partner tied to tournament readiness narratives.',
            ],
            [
                'name' => 'Bank of Africa',
                'slug' => 'bank-of-africa',
                'category' => 'Financial Services Partner',
                'tier' => 'Official Banking Partner',
                'website_url' => 'https://www.bankofafrica.ma',
                'description' => 'Bank of Africa supports payment, hospitality, and operational finance workflows across the showcase tournament environment.',
            ],
            [
                'name' => 'Inwi',
                'slug' => 'inwi',
                'category' => 'Digital Services Partner',
                'tier' => 'Official Innovation Partner',
                'website_url' => 'https://www.inwi.ma',
                'description' => 'Inwi is included in the showcase partner portfolio to represent fan-facing digital services and connected matchday support channels.',
            ],
        ];

        foreach ($definitions as $definition) {
            Partner::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'category' => $definition['category'],
                    'tier' => $definition['tier'],
                    'website_url' => $definition['website_url'],
                    'description' => $definition['description'],
                    'status' => 'active',
                ]
            );
        }
    }

    private function seedNews(array $categories, array $users): array
    {
        $definitions = [
            [
                'slug' => 'morocco-2030-host-network-moves-into-match-operations-mode',
                'category' => 'tournament-updates',
                'title' => 'Morocco 2030 Host Network Moves Into Match Operations Mode',
                'summary' => 'Venue teams, transport leads, and city command centres enter the integrated tournament operations window across the showcase schedule.',
                'body' => implode("\n\n", [
                    'The Morocco 2030 showcase platform now reflects the transition from planning into coordinated match operations across the host network.',
                    'City teams in Casablanca, Rabat, Tangier, Marrakech, Agadir, and Fes are aligned around shared transport, workforce, hospitality, and venue-readiness protocols.',
                    'The current editorial and competition dataset is structured to demonstrate how tournament information can move cleanly between operational, sporting, and public-facing channels.',
                ]),
                'published_at' => now()->subDays(3)->setTime(9, 0),
            ],
            [
                'slug' => 'group-stage-opens-with-clear-rhythm-across-casablanca-and-rabat',
                'category' => 'matchday-briefings',
                'title' => 'Group Stage Opens With Clear Rhythm Across Casablanca And Rabat',
                'summary' => 'Opening fixtures established early momentum in Groups A and B, with crowd energy and competition structure both visible across the first match window.',
                'body' => implode("\n\n", [
                    'The first completed group-stage fixtures produced immediate clarity in the standings while showcasing the balance between host-city experience and sporting intensity.',
                    'Morocco’s opening win and Spain’s controlled start in Group A have already turned the table into one of the most watched sections of the tournament overview.',
                    'On the public side, the results feed, standings tables, and match centres now carry enough real records to demonstrate the premium tournament presentation layer end to end.',
                ]),
                'published_at' => now()->subDays(2)->setTime(12, 30),
            ],
            [
                'slug' => 'quarter-final-pathways-take-shape-as-semi-final-slot-resolves',
                'category' => 'tournament-updates',
                'title' => 'Quarter-final Pathways Take Shape As First Semi-final Slot Resolves',
                'summary' => 'Completed quarter-finals have already populated one side of the semi-final bracket, while the opposite half remains open heading into the next fixture window.',
                'body' => implode("\n\n", [
                    'The showcase knockout layer now demonstrates resolved and unresolved bracket logic working side by side inside the same competition engine.',
                    'Completed quarter-finals have pushed qualified teams forward automatically, while live and scheduled fixtures still retain transparent progression labels where outcomes are not yet known.',
                    'This balance is particularly useful for evaluating the public knockout page, match operations pages, and competition services together.',
                ]),
                'published_at' => now()->subDay()->setTime(15, 15),
            ],
            [
                'slug' => 'host-city-transport-and-wayfinding-tests-complete-next-phase',
                'category' => 'host-cities',
                'title' => 'Host-city Transport And Wayfinding Tests Complete Next Phase',
                'summary' => 'City operations teams report coordinated readiness gains across rail, venue access, and inner-city supporter movement scenarios.',
                'body' => implode("\n\n", [
                    'Operational testing across the host-city network has been staged to mirror the rhythm of live match windows and high-density visitor flows.',
                    'The current showcase seed highlights Moroccan host cities not as placeholders, but as connected tournament environments with venue, city, and transport relationships surfaced directly in the public frontend.',
                    'This helps both editorial and destination pages feel like meaningful parts of the overall platform rather than isolated content sections.',
                ]),
                'published_at' => now()->subHours(14),
            ],
            [
                'slug' => 'partner-service-layer-expands-across-connectivity-and-mobility',
                'category' => 'partners',
                'title' => 'Partner Service Layer Expands Across Connectivity And Mobility',
                'summary' => 'Official partners now cover transport, financial services, sustainability, and digital connectivity within the showcase programme.',
                'body' => implode("\n\n", [
                    'The partner layer in the showcase dataset has been designed to support both public presentation and administrative visibility.',
                    'Mobility, banking, telecommunications, and infrastructure partners now appear with structured categories and tiers so that homepage modules and partner listings feel complete.',
                    'This approach keeps the platform demonstrable without weakening the real-data principle used across the rest of the public site.',
                ]),
                'published_at' => now()->subHours(6),
            ],
        ];

        $newsItems = [];

        foreach ($definitions as $definition) {
            $news = News::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'category_id' => $categories[$definition['category']]->id,
                    'author_id' => $users['journalist']->id,
                    'editor_id' => $users['chief-editor']->id,
                    'title' => $definition['title'],
                    'summary' => $definition['summary'],
                    'body' => $definition['body'],
                    'status' => 'published',
                    'visibility' => 'public',
                    'published_at' => $definition['published_at'],
                    'featured_at' => $definition['published_at']->copy()->subHour(),
                    'meta' => [
                        'source' => 'showcase-seeder',
                        'tone' => 'official',
                    ],
                ]
            );

            $newsItems[$definition['slug']] = $news;
        }

        return $newsItems;
    }

    private function seedMatches(array $groups, array $teams, array $cities, array $stadiums): array
    {
        $definitions = [
            ['code' => 'M2030-A1', 'slug' => 'morocco-vs-mexico-group-a', 'stage' => 'group', 'group' => 'A', 'home' => 'MOR', 'away' => 'MEX', 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'completed', 'when' => $this->dateAt(-12, 20), 'home_score' => 2, 'away_score' => 0],
            ['code' => 'M2030-A2', 'slug' => 'spain-vs-japan-group-a', 'stage' => 'group', 'group' => 'A', 'home' => 'ESP', 'away' => 'JPN', 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'completed', 'when' => $this->dateAt(-12, 23), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-A3', 'slug' => 'morocco-vs-japan-group-a', 'stage' => 'group', 'group' => 'A', 'home' => 'MOR', 'away' => 'JPN', 'city' => 'tangier', 'stadium' => 'grand-stade-de-tanger', 'status' => 'completed', 'when' => $this->dateAt(-10, 20), 'home_score' => 1, 'away_score' => 0],
            ['code' => 'M2030-A4', 'slug' => 'spain-vs-mexico-group-a', 'stage' => 'group', 'group' => 'A', 'home' => 'ESP', 'away' => 'MEX', 'city' => 'marrakech', 'stadium' => 'marrakech-stadium', 'status' => 'completed', 'when' => $this->dateAt(-10, 23), 'home_score' => 2, 'away_score' => 1],
            ['code' => 'M2030-A5', 'slug' => 'morocco-vs-spain-group-a', 'stage' => 'group', 'group' => 'A', 'home' => 'MOR', 'away' => 'ESP', 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'completed', 'when' => $this->dateAt(-8, 20), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-A6', 'slug' => 'japan-vs-mexico-group-a', 'stage' => 'group', 'group' => 'A', 'home' => 'JPN', 'away' => 'MEX', 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'completed', 'when' => $this->dateAt(-8, 23), 'home_score' => 2, 'away_score' => 1],

            ['code' => 'M2030-B1', 'slug' => 'france-vs-united-states-group-b', 'stage' => 'group', 'group' => 'B', 'home' => 'FRA', 'away' => 'USA', 'city' => 'agadir', 'stadium' => 'adrar-stadium', 'status' => 'completed', 'when' => $this->dateAt(-11, 20), 'home_score' => 2, 'away_score' => 1],
            ['code' => 'M2030-B2', 'slug' => 'netherlands-vs-senegal-group-b', 'stage' => 'group', 'group' => 'B', 'home' => 'NED', 'away' => 'SEN', 'city' => 'fes', 'stadium' => 'fes-stadium', 'status' => 'completed', 'when' => $this->dateAt(-11, 23), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-B3', 'slug' => 'france-vs-senegal-group-b', 'stage' => 'group', 'group' => 'B', 'home' => 'FRA', 'away' => 'SEN', 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'completed', 'when' => $this->dateAt(-9, 20), 'home_score' => 1, 'away_score' => 0],
            ['code' => 'M2030-B4', 'slug' => 'netherlands-vs-united-states-group-b', 'stage' => 'group', 'group' => 'B', 'home' => 'NED', 'away' => 'USA', 'city' => 'tangier', 'stadium' => 'grand-stade-de-tanger', 'status' => 'completed', 'when' => $this->dateAt(-9, 23), 'home_score' => 0, 'away_score' => 0],
            ['code' => 'M2030-B5', 'slug' => 'france-vs-netherlands-group-b', 'stage' => 'group', 'group' => 'B', 'home' => 'FRA', 'away' => 'NED', 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'completed', 'when' => $this->dateAt(-7, 20), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-B6', 'slug' => 'senegal-vs-united-states-group-b', 'stage' => 'group', 'group' => 'B', 'home' => 'SEN', 'away' => 'USA', 'city' => 'marrakech', 'stadium' => 'marrakech-stadium', 'status' => 'completed', 'when' => $this->dateAt(-7, 23), 'home_score' => 2, 'away_score' => 1],

            ['code' => 'M2030-C1', 'slug' => 'brazil-vs-south-korea-group-c', 'stage' => 'group', 'group' => 'C', 'home' => 'BRA', 'away' => 'KOR', 'city' => 'agadir', 'stadium' => 'adrar-stadium', 'status' => 'completed', 'when' => $this->dateAt(-10, 18), 'home_score' => 3, 'away_score' => 1],
            ['code' => 'M2030-C2', 'slug' => 'belgium-vs-croatia-group-c', 'stage' => 'group', 'group' => 'C', 'home' => 'BEL', 'away' => 'CRO', 'city' => 'fes', 'stadium' => 'fes-stadium', 'status' => 'completed', 'when' => $this->dateAt(-10, 21), 'home_score' => 0, 'away_score' => 0],
            ['code' => 'M2030-C3', 'slug' => 'brazil-vs-belgium-group-c', 'stage' => 'group', 'group' => 'C', 'home' => 'BRA', 'away' => 'BEL', 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'completed', 'when' => $this->dateAt(-6, 18), 'home_score' => 2, 'away_score' => 0],
            ['code' => 'M2030-C4', 'slug' => 'croatia-vs-south-korea-group-c', 'stage' => 'group', 'group' => 'C', 'home' => 'CRO', 'away' => 'KOR', 'city' => 'marrakech', 'stadium' => 'marrakech-stadium', 'status' => 'completed', 'when' => $this->dateAt(-6, 21), 'home_score' => 2, 'away_score' => 1],
            ['code' => 'M2030-C5', 'slug' => 'brazil-vs-croatia-group-c', 'stage' => 'group', 'group' => 'C', 'home' => 'BRA', 'away' => 'CRO', 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'completed', 'when' => $this->dateAt(-4, 18), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-C6', 'slug' => 'belgium-vs-south-korea-group-c', 'stage' => 'group', 'group' => 'C', 'home' => 'BEL', 'away' => 'KOR', 'city' => 'tangier', 'stadium' => 'grand-stade-de-tanger', 'status' => 'completed', 'when' => $this->dateAt(-4, 21), 'home_score' => 2, 'away_score' => 1],

            ['code' => 'M2030-D1', 'slug' => 'argentina-vs-germany-group-d', 'stage' => 'group', 'group' => 'D', 'home' => 'ARG', 'away' => 'GER', 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'completed', 'when' => $this->dateAt(-9, 18), 'home_score' => 1, 'away_score' => 0],
            ['code' => 'M2030-D2', 'slug' => 'england-vs-portugal-group-d', 'stage' => 'group', 'group' => 'D', 'home' => 'ENG', 'away' => 'POR', 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'completed', 'when' => $this->dateAt(-9, 21), 'home_score' => 2, 'away_score' => 2],
            ['code' => 'M2030-D3', 'slug' => 'argentina-vs-portugal-group-d', 'stage' => 'group', 'group' => 'D', 'home' => 'ARG', 'away' => 'POR', 'city' => 'agadir', 'stadium' => 'adrar-stadium', 'status' => 'completed', 'when' => $this->dateAt(-5, 18), 'home_score' => 2, 'away_score' => 1],
            ['code' => 'M2030-D4', 'slug' => 'england-vs-germany-group-d', 'stage' => 'group', 'group' => 'D', 'home' => 'ENG', 'away' => 'GER', 'city' => 'fes', 'stadium' => 'fes-stadium', 'status' => 'completed', 'when' => $this->dateAt(-5, 21), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-D5', 'slug' => 'argentina-vs-england-group-d', 'stage' => 'group', 'group' => 'D', 'home' => 'ARG', 'away' => 'ENG', 'city' => 'marrakech', 'stadium' => 'marrakech-stadium', 'status' => 'completed', 'when' => $this->dateAt(-3, 18), 'home_score' => 0, 'away_score' => 0],
            ['code' => 'M2030-D6', 'slug' => 'portugal-vs-germany-group-d', 'stage' => 'group', 'group' => 'D', 'home' => 'POR', 'away' => 'GER', 'city' => 'tangier', 'stadium' => 'grand-stade-de-tanger', 'status' => 'completed', 'when' => $this->dateAt(-3, 21), 'home_score' => 2, 'away_score' => 1],

            ['code' => 'M2030-QF1', 'slug' => 'morocco-vs-france-quarter-final', 'stage' => 'quarter_final', 'group' => null, 'home' => 'MOR', 'away' => 'FRA', 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'completed', 'when' => $this->dateAt(-1, 20), 'home_score' => 2, 'away_score' => 1],
            ['code' => 'M2030-QF2', 'slug' => 'senegal-vs-spain-quarter-final', 'stage' => 'quarter_final', 'group' => null, 'home' => 'SEN', 'away' => 'ESP', 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'completed', 'when' => $this->dateAt(-1, 23), 'home_score' => 0, 'away_score' => 1],
            ['code' => 'M2030-QF3', 'slug' => 'brazil-vs-portugal-quarter-final', 'stage' => 'quarter_final', 'group' => null, 'home' => 'BRA', 'away' => 'POR', 'city' => 'tangier', 'stadium' => 'grand-stade-de-tanger', 'status' => 'live', 'when' => now()->subMinutes(35), 'home_score' => 1, 'away_score' => 1],
            ['code' => 'M2030-QF4', 'slug' => 'argentina-vs-croatia-quarter-final', 'stage' => 'quarter_final', 'group' => null, 'home' => 'ARG', 'away' => 'CRO', 'city' => 'marrakech', 'stadium' => 'marrakech-stadium', 'status' => 'scheduled', 'when' => $this->dateAt(1, 21), 'home_score' => null, 'away_score' => null],

            ['code' => 'M2030-SF1', 'slug' => 'semi-final-one', 'stage' => 'semi_final', 'group' => null, 'home' => null, 'away' => null, 'city' => 'rabat', 'stadium' => 'prince-moulay-abdellah', 'status' => 'scheduled', 'when' => $this->dateAt(4, 20), 'home_score' => null, 'away_score' => null],
            ['code' => 'M2030-SF2', 'slug' => 'semi-final-two', 'stage' => 'semi_final', 'group' => null, 'home' => null, 'away' => null, 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'scheduled', 'when' => $this->dateAt(5, 20), 'home_score' => null, 'away_score' => null],
            ['code' => 'M2030-FINAL', 'slug' => 'morocco-2030-final', 'stage' => 'final', 'group' => null, 'home' => null, 'away' => null, 'city' => 'casablanca', 'stadium' => 'mohammed-v', 'status' => 'scheduled', 'when' => $this->dateAt(8, 21), 'home_score' => null, 'away_score' => null],
        ];

        $matches = [];

        foreach ($definitions as $index => $definition) {
            $matches[$definition['code']] = MatchFixture::query()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'stadium_id' => $stadiums[$definition['stadium']]->id,
                    'city_id' => $cities[$definition['city']]->id,
                    'group_id' => $definition['group'] ? $groups[$definition['group']]->id : null,
                    'home_team_id' => $definition['home'] ? $teams[$definition['home']]->id : null,
                    'away_team_id' => $definition['away'] ? $teams[$definition['away']]->id : null,
                    'slug' => $definition['slug'],
                    'stage_type' => $definition['stage'],
                    'round_number' => $index + 1,
                    'match_date' => $definition['when'],
                    'timezone' => self::TIMEZONE,
                    'status' => $definition['status'],
                    'attendance' => $definition['status'] === 'scheduled' ? null : 42000 + ($index * 600),
                    'home_score' => $definition['home_score'],
                    'away_score' => $definition['away_score'],
                    'home_penalty_score' => null,
                    'away_penalty_score' => null,
                    'extra_time_played' => false,
                    'meta' => ['showcase' => true],
                    'published_at' => now(),
                ]
            );
        }

        return $matches;
    }

    private function seedMatchEvents(array $matches, array $teams, array $players): void
    {
        $definitions = [
            'M2030-A1' => [
                ['minute' => 0, 'period' => 'pre_match', 'event_type' => 'kickoff', 'description' => 'Opening whistle in Casablanca.'],
                ['team' => 'MOR', 'player' => 'Youssef En-Nesyri', 'minute' => 18, 'period' => 'first_half', 'event_type' => 'goal', 'description' => 'Morocco convert an early cross from the right channel.'],
                ['team' => 'MEX', 'player' => 'Edson Alvarez', 'minute' => 42, 'period' => 'first_half', 'event_type' => 'yellow_card', 'description' => 'Late midfield challenge brings the first booking.'],
                ['minute' => 45, 'period' => 'half_time', 'event_type' => 'halftime', 'description' => 'Half-time in Casablanca.'],
                ['team' => 'MOR', 'player' => 'Hakim Ziyech', 'minute' => 67, 'period' => 'second_half', 'event_type' => 'goal', 'description' => 'A precise left-footed finish doubles the lead.'],
                ['team' => 'MEX', 'player' => 'Hirving Lozano', 'related_player' => 'Santiago Gimenez', 'minute' => 73, 'period' => 'second_half', 'event_type' => 'substitution', 'description' => 'Mexico refresh the front line.'],
                ['minute' => 90, 'period' => 'post_match', 'event_type' => 'fulltime', 'description' => 'Full-time: Morocco begin the showcase tournament with three points.'],
            ],
            'M2030-QF1' => [
                ['minute' => 0, 'period' => 'pre_match', 'event_type' => 'kickoff', 'description' => 'Quarter-final one is underway.'],
                ['team' => 'MOR', 'player' => 'Achraf Hakimi', 'minute' => 14, 'period' => 'first_half', 'event_type' => 'goal', 'description' => 'Hakimi arrives late at the far post to put Morocco ahead.'],
                ['team' => 'FRA', 'player' => 'Kylian Mbappe', 'minute' => 33, 'period' => 'first_half', 'event_type' => 'goal', 'description' => 'France respond through a quick transition attack.'],
                ['minute' => 45, 'period' => 'half_time', 'event_type' => 'halftime', 'description' => 'Level at the interval.'],
                ['team' => 'MOR', 'player' => 'Azzedine Ounahi', 'minute' => 71, 'period' => 'second_half', 'event_type' => 'goal', 'description' => 'Ounahi finishes from the edge of the area to restore the lead.'],
                ['team' => 'FRA', 'player' => 'Adrien Rabiot', 'minute' => 82, 'period' => 'second_half', 'event_type' => 'yellow_card', 'description' => 'France receive a late caution chasing the equaliser.'],
                ['minute' => 90, 'period' => 'post_match', 'event_type' => 'fulltime', 'description' => 'Morocco progress into the first resolved semi-final slot.'],
            ],
            'M2030-QF3' => [
                ['minute' => 0, 'period' => 'pre_match', 'event_type' => 'kickoff', 'description' => 'Live quarter-final action in Tangier.'],
                ['team' => 'BRA', 'player' => 'Vinicius Junior', 'minute' => 22, 'period' => 'first_half', 'event_type' => 'goal', 'description' => 'Brazil strike first through a direct transition on the left side.'],
                ['team' => 'POR', 'player' => 'Cristiano Ronaldo', 'minute' => 39, 'period' => 'first_half', 'event_type' => 'penalty', 'description' => 'Portugal equalise from the spot before the break.'],
                ['minute' => 45, 'period' => 'half_time', 'event_type' => 'halftime', 'description' => 'Half-time in Tangier with the tie level.'],
                ['team' => 'BRA', 'player' => 'Lucas Paqueta', 'minute' => 51, 'period' => 'second_half', 'event_type' => 'yellow_card', 'description' => 'Brazil are booked early in the second half.'],
            ],
        ];

        foreach ($definitions as $matchCode => $events) {
            $match = $matches[$matchCode];
            $match->events()->delete();

            foreach ($events as $index => $event) {
                MatchEvent::query()->create([
                    'match_id' => $match->id,
                    'team_id' => isset($event['team']) ? $teams[$event['team']]->id : null,
                    'player_id' => isset($event['player']) ? $players[$event['team']][$event['player']]->id : null,
                    'related_player_id' => isset($event['related_player']) ? $players[$event['team']][$event['related_player']]->id : null,
                    'minute' => $event['minute'],
                    'extra_minute' => $event['extra_minute'] ?? null,
                    'period' => $event['period'],
                    'event_type' => $event['event_type'],
                    'description' => $event['description'],
                    'payload' => ['showcase' => true],
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }

    private function seedMatchStatistics(array $matches, array $teams): void
    {
        $definitions = [
            'M2030-A1' => [
                'MOR' => ['possession_percentage' => '56', 'shots' => '12', 'shots_on_target' => '6', 'passes' => '478', 'pass_accuracy' => '88', 'fouls' => '11', 'yellow_cards' => '1', 'red_cards' => '0', 'corners' => '5', 'offsides' => '2', 'saves' => '3'],
                'MEX' => ['possession_percentage' => '44', 'shots' => '8', 'shots_on_target' => '3', 'passes' => '389', 'pass_accuracy' => '82', 'fouls' => '14', 'yellow_cards' => '2', 'red_cards' => '0', 'corners' => '4', 'offsides' => '1', 'saves' => '4'],
            ],
            'M2030-QF1' => [
                'MOR' => ['possession_percentage' => '48', 'shots' => '11', 'shots_on_target' => '5', 'passes' => '421', 'pass_accuracy' => '84', 'fouls' => '13', 'yellow_cards' => '1', 'red_cards' => '0', 'corners' => '4', 'offsides' => '2', 'saves' => '2'],
                'FRA' => ['possession_percentage' => '52', 'shots' => '13', 'shots_on_target' => '4', 'passes' => '457', 'pass_accuracy' => '87', 'fouls' => '10', 'yellow_cards' => '2', 'red_cards' => '0', 'corners' => '6', 'offsides' => '1', 'saves' => '3'],
            ],
            'M2030-QF3' => [
                'BRA' => ['possession_percentage' => '54', 'shots' => '7', 'shots_on_target' => '3', 'passes' => '301', 'pass_accuracy' => '89', 'fouls' => '5', 'yellow_cards' => '1', 'red_cards' => '0', 'corners' => '3', 'offsides' => '1', 'saves' => '2'],
                'POR' => ['possession_percentage' => '46', 'shots' => '5', 'shots_on_target' => '2', 'passes' => '263', 'pass_accuracy' => '85', 'fouls' => '7', 'yellow_cards' => '0', 'red_cards' => '0', 'corners' => '2', 'offsides' => '0', 'saves' => '2'],
            ],
        ];

        foreach ($definitions as $matchCode => $teamStatistics) {
            $match = $matches[$matchCode];
            $match->statistics()->delete();

            foreach ($teamStatistics as $teamCode => $metrics) {
                foreach ($metrics as $metricKey => $value) {
                    MatchStatistic::query()->create([
                        'match_id' => $match->id,
                        'team_id' => $teams[$teamCode]->id,
                        'metric_key' => $metricKey,
                        'metric_value' => $value,
                        'display_value' => str_contains($metricKey, 'percentage') || $metricKey === 'pass_accuracy' ? $value.'%' : $value,
                        'context' => 'full_time',
                    ]);
                }
            }
        }
    }

    private function seedMatchLineups(array $matches, array $teams, array $players): void
    {
        $definitions = [
            'M2030-A1' => ['MOR', 'MEX'],
            'M2030-QF1' => ['MOR', 'FRA'],
            'M2030-QF3' => ['BRA', 'POR'],
        ];

        foreach ($definitions as $matchCode => $teamCodes) {
            $match = $matches[$matchCode];
            $match->lineups()->delete();

            foreach ($teamCodes as $teamCode) {
                $teamPlayers = array_values($players[$teamCode]);

                foreach ($teamPlayers as $index => $player) {
                    MatchLineup::query()->create([
                        'match_id' => $match->id,
                        'team_id' => $teams[$teamCode]->id,
                        'player_id' => $player->id,
                        'lineup_type' => $index < 5 ? 'starting' : 'bench',
                        'sort_order' => $index + 1,
                        'position' => match ($player->position) {
                            'goalkeeper' => 'GK',
                            'defender' => 'DF',
                            'midfielder' => 'MF',
                            default => 'FW',
                        },
                        'shirt_number' => $player->shirt_number,
                        'formation_slot' => $index < 5 ? (string) ($index + 1) : null,
                        'is_captain' => (bool) $player->is_captain,
                        'is_goalkeeper' => $player->position === 'goalkeeper',
                    ]);
                }
            }
        }
    }

    private function seedKnockoutProgressions(array $matches): void
    {
        $definitions = [
            ['source' => 'M2030-QF1', 'target' => 'M2030-SF1', 'type' => 'winner', 'slot' => 'home', 'notes' => 'Winner of quarter-final one advances to semi-final one home slot.'],
            ['source' => 'M2030-QF2', 'target' => 'M2030-SF1', 'type' => 'winner', 'slot' => 'away', 'notes' => 'Winner of quarter-final two advances to semi-final one away slot.'],
            ['source' => 'M2030-QF3', 'target' => 'M2030-SF2', 'type' => 'winner', 'slot' => 'home', 'notes' => 'Winner of quarter-final three advances to semi-final two home slot.'],
            ['source' => 'M2030-QF4', 'target' => 'M2030-SF2', 'type' => 'winner', 'slot' => 'away', 'notes' => 'Winner of quarter-final four advances to semi-final two away slot.'],
            ['source' => 'M2030-SF1', 'target' => 'M2030-FINAL', 'type' => 'winner', 'slot' => 'home', 'notes' => 'Winner of semi-final one advances to the final home slot.'],
            ['source' => 'M2030-SF2', 'target' => 'M2030-FINAL', 'type' => 'winner', 'slot' => 'away', 'notes' => 'Winner of semi-final two advances to the final away slot.'],
        ];

        foreach ($definitions as $definition) {
            KnockoutProgression::query()->updateOrCreate(
                [
                    'source_match_id' => $matches[$definition['source']]->id,
                    'target_match_id' => $matches[$definition['target']]->id,
                    'progression_type' => $definition['type'],
                    'team_slot' => $definition['slot'],
                ],
                ['notes' => $definition['notes']]
            );
        }
    }

    private function seedEditorialWorkflows(array $newsItems, array $users): void
    {
        foreach ($newsItems as $news) {
            $news->editorialWorkflows()->delete();

            EditorialWorkflow::query()->create([
                'workflowable_type' => News::class,
                'workflowable_id' => $news->id,
                'submitted_by' => $users['journalist']->id,
                'reviewed_by' => $users['chief-editor']->id,
                'status' => 'published',
                'current_step' => 'published',
                'notes' => 'Showcase editorial item prepared for public presentation and approved for the seeded demo environment.',
                'payload' => ['source' => 'showcase-seeder'],
                'submitted_at' => $news->published_at?->copy()->subHours(8),
                'reviewed_at' => $news->published_at?->copy()->subHours(2),
                'published_at' => $news->published_at,
            ]);
        }
    }

    private function seedAuditLogs(array $users, array $matches, array $newsItems, array $groups): void
    {
        $definitions = [
            [
                'user' => $users['chief-editor'],
                'auditable' => reset($newsItems),
                'action' => 'published',
                'route_name' => 'admin.news.publish',
                'occurred_at' => now()->subHours(6),
                'new_values' => ['status' => 'published'],
            ],
            [
                'user' => $users['competition-manager'],
                'auditable' => $matches['M2030-QF1'],
                'action' => 'updated',
                'route_name' => 'admin.matches.update',
                'occurred_at' => now()->subHours(3),
                'new_values' => ['status' => 'completed', 'home_score' => 2, 'away_score' => 1],
            ],
            [
                'user' => $users['competition-manager'],
                'auditable' => $groups['A'],
                'action' => 'standings_recalculated',
                'route_name' => 'admin.matches.recalculate-standings',
                'occurred_at' => now()->subHours(2),
                'new_values' => ['group' => 'A'],
            ],
            [
                'user' => $users['competition-manager'],
                'auditable' => $matches['M2030-SF1'],
                'action' => 'knockout_progression_applied',
                'route_name' => 'admin.matches.propagate-knockout',
                'occurred_at' => now()->subHour(),
                'new_values' => ['home_slot' => $matches['M2030-SF1']->fresh()->slotLabel('home'), 'away_slot' => $matches['M2030-SF1']->fresh()->slotLabel('away')],
            ],
        ];

        foreach ($definitions as $definition) {
            AuditLog::query()->updateOrCreate(
                [
                    'user_id' => $definition['user']->id,
                    'auditable_type' => $definition['auditable']::class,
                    'auditable_id' => $definition['auditable']->id,
                    'action' => $definition['action'],
                    'route_name' => $definition['route_name'],
                ],
                [
                    'old_values' => null,
                    'new_values' => $definition['new_values'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Showcase Seeder',
                    'occurred_at' => $definition['occurred_at'],
                ]
            );
        }
    }

    private function syncTranslation(Model $model, string $field, string $value): void
    {
        Translation::query()->updateOrCreate(
            [
                'translatable_type' => $model::class,
                'translatable_id' => $model->getKey(),
                'language_id' => $this->english->id,
                'field' => $field,
            ],
            [
                'value' => $value,
                'meta' => ['source' => 'showcase-seeder'],
            ]
        );
    }

    private function dateAt(int $dayOffset, int $hour): CarbonInterface
    {
        return now()
            ->timezone(self::TIMEZONE)
            ->startOfDay()
            ->addDays($dayOffset)
            ->setTime($hour, 0);
    }
}
