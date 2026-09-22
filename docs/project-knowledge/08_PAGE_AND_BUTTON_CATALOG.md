# Catalogue pages et actions

## Pages publiques

| URL | Route | Controller | Vue | Acces | Donnees | Actions visibles | Form/validation | Tests |
|---|---|---|---|---|---|---|---|---|
| `/` | `home` | `Site\HomeController` | `public/home.blade.php` | Tous | latestNews, upcomingMatches, recentResults, standingsPreview, partnersPreview | liens matches/results/standings/news/map/partners | Newsletter dans `routes/web.php`, email required/email/max255 | `PublicFrontendPagesTest` |
| `/news` | `news.index` | `Site\NewsController@index` | `public/news/index.blade.php` | Tous | News published public | liens detail news, pagination | N/A | `PublicNewsVisibilityTest` |
| `/news/{slug}` | `news.show` | `Site\NewsController@show` | `public/news/show.blade.php` | Tous | News published public + related | Back to News, More News | N/A | `PublicFrontendPagesTest` |
| `/matches` | `matches.index` | `Site\MatchController@index` | `public/matches/index.blade.php` | Tous | FD-WC matches pagines | liens detail match | N/A | `FootballDataPublicConsistencyTest` |
| `/matches/{slug}` | `matches.show` | `Site\MatchController@show` | `public/matches/show.blade.php` | Tous | match, events, stats, lineups | match centre links | N/A | `PublicFrontendPagesTest` |
| `/results` | `results.index` | `Site\MatchController@results` | `public/matches/results.blade.php` | Tous | completed matches | detail match | N/A | `FootballDataMatchRefreshPublicTest` |
| `/standings` | `standings.index` | `Site\StandingsController@index` | `public/standings/index.blade.php` | Tous | groups/standings | group links | N/A | `FootballDataMultiGroupStandingsTest` |
| `/standings/group/{code}` | `standings.show` | `Site\StandingsController@show` | `public/standings/show.blade.php` | Tous | group standings | back/links | N/A | `FootballDataStandingsConsistencyTest` |
| `/teams` | `teams.index` | `Site\TeamController@index` | `public/teams/index.blade.php` | Tous | teams active/FD scoped | detail team | N/A | `FootballDataPlayerTeamDisplayTest` |
| `/players` | `players.index` | `Site\PlayerController@index` | `public/players/index.blade.php` | Tous | active players FD scoped | detail player | N/A | `FootballDataPlayerTeamDisplayTest` |
| `/partners` | `partners.index` | `Site\PartnerController@index` | `public/partners/index.blade.php` | Tous | `Partner::publiclyVisible()` | partner website links if present | N/A | `PublicPartnersVisibilityTest` |
| `/cities` | `cities.index` | `Site\CityController@index` | `public/cities/index.blade.php` | Tous | active cities | detail city | N/A | `PublicFrontendPagesTest` |
| `/stadiums` | `stadiums.index` | `Site\StadiumController@index` | `public/stadiums/index.blade.php` | Tous | active stadiums | detail stadium | N/A | `PublicFrontendPagesTest` |
| `/map` | `map.index` | `Site\MapController` | `public/map/index.blade.php` | Tous | cities/stadiums coordinates | city/stadium links | N/A | `PublicSearchMapLocaleTest` |
| `/knockout` | `knockout.index` | `Site\KnockoutController` | `public/knockout/index.blade.php` | Tous | knockout matches/progressions | match links | N/A | `PublicFrontendPagesTest` |
| `/bracket` | Route not found | Not implemented | Not implemented | N/A | N/A | N/A | N/A | N/A |
| `/search` | `search.index` | `Site\SearchController` | `public/search/index.blade.php` | Tous | grouped public search | search field/result links | query input | `PublicSearchMapLocaleTest` |
| `/account` | `account.index` | `Site\AccountController@index` | `public/account/index.blade.php` | Public User auth | account summary | account nav/logout | N/A | `PublicAccountPageTest` |
| `/account/profile` | `account.profile` | `AccountController@profile/updateProfile` | `public/account/profile.blade.php` | Public User | user profile | Save profile | `UpdateProfileRequest` | `PublicStaffAuthSplitTest` |
| `/account/settings` | `account.settings` | `AccountController@settings/updateSettings` | `public/account/settings.blade.php` | Public User | preferences | Save settings | `UpdateSettingsRequest` | `PublicAccountPageTest` |

## Pages admin principales

| URL | Route | Controller | Vue | Acces | Actions/boutons | Validation | Tests |
|---|---|---|---|---|---|---|---|
| `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController` | `admin/dashboard/index.blade.php` | Staff autorise | KPIs, links modules | N/A | `AdminDashboardRoleWidgetContractTest` |
| `/admin/users` | `admin.users.index` | `UserController@index` | `admin/users/index.blade.php` | `users.view/manage` | Create/Edit/Delete/View | `StoreUserRequest`, `UpdateUserRequest` | `AdminUsers*` |
| `/admin/roles` | `admin.roles.index` | `RoleController@index` | `admin/roles/index.blade.php` | roles permission | CRUD roles | `StoreRoleRequest`, `UpdateRoleRequest` | `AdminAccessControlContractTest` |
| `/admin/news` | `admin.news.index` | `NewsController@index` | `admin/news/index.blade.php` | news permissions | Create, View, Edit, workflow actions | filters | `AdminNewsIndexUiTest` |
| `/admin/news/create` | `admin.news.create` | `NewsController@create` | `admin/news/create.blade.php` | `news.manage` | Save Draft, Cancel | `StoreNewsRequest` | `NewsCategoryReadinessTest` |
| `/admin/news-categories` | `admin.news-categories.index` | `NewsCategoryController@index` | `admin/news-categories/index.blade.php` | `news.review/publish` | Create/Edit/Delete | category requests | `NewsCategoryReadinessTest` |
| `/admin/teams` | `admin.teams.index` | `TeamController@index` | `admin/teams/index.blade.php` | `teams.manage` | CRUD | `StoreTeamRequest`, `UpdateTeamRequest` | `TeamCrudFlowTest` |
| `/admin/players` | `admin.players.index` | `PlayerController@index` | `admin/players/index.blade.php` | `players.manage` | CRUD | `StorePlayerRequest`, `UpdatePlayerRequest` | `FootballDataPlayerTeamDisplayTest` |
| `/admin/matches` | `admin.matches.index` | `MatchFixtureController@index` | `admin/matches/index.blade.php` | `matches.manage` | CRUD, recalc standings, propagate knockout | match/event/stat/lineup requests | `MatchCrudFlowTest` |
| `/admin/groups` | `admin.groups.index` | `GroupController@index` | `admin/groups/index.blade.php` | `groups.manage`/standing roles | CRUD groups | group requests | `GroupAuthorizationTest` |
| `/admin/cities` | `admin.cities.index` | `CityController@index` | `admin/cities/index.blade.php` | `cities.manage` | CRUD | city requests | `VenueManagerCitiesTest` |
| `/admin/stadiums` | `admin.stadiums.index` | `StadiumController@index` | `admin/stadiums/index.blade.php` | `stadiums.manage` | CRUD | stadium requests | `VenueManagerStadiumsTest` |
| `/admin/partners` | `admin.partners.index` | `PartnerController@index` | `admin/partners/index.blade.php` | `partners.manage` | CRUD, logo upload | `StorePartnerRequest`, `UpdatePartnerRequest`, logo image max 2048 | `SponsorManagerPartnersTest` |
| `/admin/media-files` | `admin.media-files.index` | `MediaFileController@index` | `admin/media-files/index.blade.php` | `media.manage` | upload, attach, archive, restore, edit metadata | media requests | `MediaFileUploadMvpTest` |
| `/admin/media-readiness` | `admin.media-readiness.index` | `MediaReadinessController` | `admin/media-readiness/index.blade.php` | `media.manage` | read-only checklist | N/A | `MediaReadinessAdminModuleTest` |
| `/admin/interface-translations` | `admin.interface-translations.index` | `InterfaceTranslationController@index` | `admin/interface-translations/index.blade.php` | `translations.manage` | edit translations, missing report | update request | `TranslationAdminModulesTest` |
| `/admin/audit-logs` | `admin.audit-logs.index` | `AuditLogController@index` | `admin/audit-logs/index.blade.php` | `audit-logs.view` | View logs | N/A | `AuditLogsAdminModuleTest` |
| `/admin/settings` | `admin.settings.index` | `SettingController@index` | `admin/settings/index.blade.php` | `settings.manage` | Edit/update | `UpdateSettingRequest` | `SettingsContactAdminModulesTest` |
| `/admin/football-data-import` | `admin.football-data-import.index` | `FootballDataImportController@index` | `admin/football-data-import/index.blade.php` | super/platform admin | Preview, run import, reconcile, teams, structure, squads | typed confirmations + checkbox | `FootballData*Test` |

## Common errors

- 403: role/permission insufficient.
- 404 public detail: record excluded by FD-WC/public visibility scope.
- Validation error: missing required fields or invalid media type/size.
- API 429: football-data rate limit.
