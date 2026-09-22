# Rapport technique par module

| Module | Objectif | Acteurs | Routes | Controllers | Models/tables | Vues/services | DB impact | Tests |
|---|---|---|---|---|---|---|---|---|
| Auth et compte public | Login/register, profil public | Guest, Public User, Staff | `/login`, `/register`, `/account*` | `PublicAuthenticatedSessionController`, `PublicRegisteredUserController`, `AccountController` | `users`, `user_favorites`, `user_notifications` | `public/auth/*`, `public/account/*` | users public/preferences | `PublicStaffAuthSplitTest`, `PublicAccountPageTest` |
| RBAC/users/roles | Controle admin | Super Admin, Platform Admin | `admin.users.*`, `admin.roles.*` | `UserController`, `RoleController` | `users`, `roles`, `permissions`, pivots | admin users/roles, policies | roles/permissions | `AdminAccessControlContractTest`, `StrictRoleVisibilityMatrixTest` |
| News/editorial | Workflow publication | Journalist, Chief Editor | `admin.news.*`, public `news.*` | `NewsController`, `NewsWorkflowController` | `news`, `news_categories`, `editorial_workflows` | `NewsWorkflowManager`, `NewsMediaStorage` | articles/workflows/media paths | `AdminNewsJournalistWorkflowTest`, `PublicNewsVisibilityTest` |
| Football-data | Import/reconcile WC | Super/Platform Admin, API | admin football-data, CLI commands | `FootballDataImportController` | `matches`, `teams`, `players` | ExternalFootball services | status/scores/provenance/squads | `FootballData*Test` |
| Matches/resultats/standings | Competition public/admin | Match Manager, public | `/matches`, `/results`, `/standings`, admin matches | `MatchController`, `StandingsController`, `MatchFixtureController` | `matches`, `standings`, `groups` | Sports services | fixtures/scores/table | `MatchCrudFlowTest`, `FootballDataMultiGroupStandingsTest` |
| Teams/players/coaches | Equipes et effectifs | Team Manager, public | `/teams`, `/players`, admin teams/players | `TeamController`, `PlayerController` | `teams`, `players` | public media/content helpers | teams, players, coach_name | `FootballDataPlayerTeamDisplayTest` |
| Cities/stadiums/map | Sites hotes | Venue Manager, public | `/cities`, `/stadiums`, `/map` | City/Stadium/Map controllers | `cities`, `stadiums`, `matches` | public views | venues/geolocation | `VenueManager*`, `PublicSearchMapLocaleTest` |
| Partners/logos | Sponsors publics/admin | Sponsor Manager | `/partners`, admin partners | `PartnerController` | `partners` | Storage public disk | logo_path/status | `SponsorManagerPartnersTest`, `PublicPartnerLogoDisplayTest` |
| Media Review | Etat media | Media Manager | `admin.media-files.*`, `admin.media-readiness.index` | `MediaFileController`, `MediaReadinessController` | `media_files`, `media_relations` | `AssetFallback` | uploads/relations | `MediaReadinessAdminModuleTest` |
| Translations | Langues interface | Translator | `language.switch`, admin languages/translations | `LanguageController`, `InterfaceTranslationController` | `languages`, `interface_translations`, `translations` | `DatabaseTranslationLoader`, `PublicLocale` | textes localises | `TranslationAdminModulesTest`, `PublicMultilingualCompletionTest` |
| Analytics | Dashboard visits/sports | Analyst, admins | dashboard, audit | `DashboardController` | `visitor_analytics`, `sports_analytics_snapshots` | `TrackVisitorAnalytics` | page_view stats | `VisitorAnalyticsTrackingTest` |
| Audit logs | Historique admin | Analyst/Super/Platform | `admin.audit-logs.*` | `AuditLogController`, `AdminController` | `audit_logs` | `AuditLogger` | action snapshots | `AuditLogsAdminModuleTest` |
| Settings | Parametres | Super/Platform | `admin.settings.*` | `SettingController` | `settings` | admin settings views | config DB | `SettingsContactAdminModulesTest` |
| Public search | Recherche publique | Public | `/search` | `SearchController` | multi tables | `PublicSearchService` | read-only | `PublicSearchMapLocaleTest` |
| Homepage | Synthese publique | Public | `/` | `HomeController` | news/matches/teams/cities/stadiums/partners | `public/home.blade.php` | read-only | `PublicFrontendPagesTest` |

## Risques transverses

- RBAC: toute nouvelle route admin doit avoir policy/middleware/test.
- API: football-data peut retourner 429.
- Media: toujours valider MIME/taille et chemins de suppression.
- Public visibility: news draft/private ne doivent jamais apparaitre.
