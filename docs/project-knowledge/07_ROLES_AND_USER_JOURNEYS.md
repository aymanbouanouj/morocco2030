# Roles et parcours utilisateurs

Source: lecture DB `users`, `roles`, `permissions` sans passwords/hashes, `routes/admin.php`, policies et tests RBAC.

| Role | Ce qu'il voit | Routes/modules autorises principaux | Routes interdites typiques | Workflow | Impact public | Tests |
|---|---|---|---|---|---|---|
| Guest | Site public, login/register | `/`, `/news`, `/matches`, `/results`, `/standings`, `/teams`, `/players`, `/cities`, `/stadiums`, `/partners`, `/map`, `/search` | `/admin/*`, `/account` redirige login | Consulter, s'inscrire, se connecter | Aucun ecriture sauf newsletter/register | `PublicFrontendPagesTest`, `PublicStaffAuthSplitTest` |
| Public User | Site + compte public | `/account`, profile/settings/favorites/notifications | `/admin/*` | Gerer profil/preferences | Favoris/notifications seulement | `PublicAccountPageTest`, `PublicUserAccountAccessTest` |
| Super Admin | Tous modules admin | users, roles, audit, settings, football-data, all CRUD | N/A selon `Gate::before` | Administration globale | Peut tout modifier/publier/importer | `StrictRoleVisibilityMatrixTest` |
| Platform Admin | Operations larges sans identity RBAC | matches, teams, players, venues, news, partners, settings, audit, football-data | users/roles/permissions manage | Piloter plateforme | Changements publics larges | `AdminDirectRouteAuthorizationContractTest` |
| Chief Editor | Editorial | news, news-categories, media | matches, users, settings, audit | Revoir, approuver, publier news | News publiees visibles | `AdminNewsJournalistWorkflowTest` |
| Journalist | Editorial creation | news index/create/edit own draft, media | publish, news categories, users | Creer draft, submit review | Public seulement apres publication par editor | `NewsPublishingWorkflowTest` |
| Match Manager | Competition matches | matches, groups/standings selon tests | news, users | Gerer fixtures/events/statistics/lineups | Modifie pages matches/resultats/standings | `MatchCrudFlowTest`, `MatchOperationsTest` |
| Team Manager | Equipes/joueurs | teams, players, media | matches, news, users | Gerer equipes/joueurs | Modifie teams/players publics | `TeamCrudFlowTest` |
| Venue Manager | Villes/stades | cities, stadiums, media readiness | matches, users, news | Gerer host cities/stadiums | Modifie pages villes/stades/map | `VenueManagerCitiesTest`, `VenueManagerStadiumsTest` |
| Sponsor Manager | Partenaires | partners, media | news, football-data, users | Gerer partenaires/logos | Partenaires visibles si active | `SponsorManagerPartnersTest` |
| Media Manager | Media | media-files, media-readiness | news, audit, settings | Uploader/attacher/archiver media | Media lie aux entites publiques | `MediaManagerMediaReviewTest` |
| Translator | Localisation | languages, interface-translations | media/news/matches | Modifier traductions | Libelles publics traduits | `TranslationAdminModulesTest` |
| Analyst | Analytics/audit | dashboard analytics, audit logs | CRUD modules | Lire indicateurs/logs | Aucun direct | `AdminDashboardWidgetVisibilityByRoleTest` |
| Support Agent | Messages/support | contact-messages, notifications permission | news, audit | Traiter messages contact | Aucun direct sauf support | `SettingsContactAdminModulesTest` |

## Permissions observees

- `super-admin`: 25 permissions, dont `users.manage`, `roles.manage`, `permissions.manage`.
- `platform-admin`: operations larges sans permissions identity.
- `chief-editor`: `news.manage`, `news.review`, `news.publish`, `media.manage`.
- `journalist`: `news.manage`, `media.manage`.
- `match-manager`: `matches.manage`, `standings.manage`.
- `team-manager`: `teams.manage`, `players.manage`, `media.manage`.
- `venue-manager`: `cities.manage`, `stadiums.manage`, `media.manage`.
- `sponsor-manager`: `partners.manage`, `media.manage`.
- `media-manager`: `media.manage`.
- `translator`: `languages.manage`, `translations.manage`.
- `analyst`: `analytics.view`, `audit-logs.view`.
- `support-agent`: `contact-messages.manage`, `notifications.manage`.

## Remarques

- Les routes admin passent par `auth` + `admin`.
- Les policies appellent des permissions granulaires.
- Les comptes publics n'ont pas de roles dans l'audit DB.
