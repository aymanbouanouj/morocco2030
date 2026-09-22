# Guide de structure des dossiers

| Dossier/fichier | Role | Fichiers importants | Connexions | Modification sure | Risques |
|---|---|---|---|---|---|
| `app/` | Code applicatif Laravel | Controllers, Models, Services, Policies | Autoload PSR-4 `App\` | Modifier seulement avec tests cibles | Changement large impacte routes/admin/public. |
| `app/Console/Commands/` | Commandes Artisan custom | `ReconcileFootballDataMatches`, `CollectFootballDataSquads`, `ImportFootballDataSquadsFromCache`, `Morocco2030RetentionReportCommand` | Artisan CLI, services | Ajouter commandes idempotentes | Ne pas lancer squads sans controle 429. |
| `app/Http/Controllers/` | Controllers base | `Controller.php` | Routes | Rarement modifier base | Impact global. |
| `app/Http/Controllers/Admin/` | Back-office | `DashboardController`, `NewsController`, `FootballDataImportController`, CRUD modules | `routes/admin.php`, `routes/web.php`, policies | Modifier module precis | Risque RBAC ou data mutation. |
| `app/Http/Controllers/Site/` | Site public | `HomeController`, `SiteController`, `MatchController`, `NewsController`, `AccountController` | `routes/public.php`, public views | Modifier scopes publics | Risque afficher draft/private data. |
| `app/Models/` | Eloquent models | `User`, `Role`, `Permission`, `MatchFixture`, `Team`, `News`, `Partner` | DB tables, relationships | Ajouter scopes/methodes ciblees | Fillable/casts incorrects exposent data. |
| `app/Services/` | Logique metier | `ExternalFootball`, `Sports`, `Site` | Controllers/commands | Tester service isole | Risque ecriture DB/API. |
| `app/Services/ExternalFootball/` | Integration football-data | `FootballDataClient`, reconciliation/import/squad services | Config services, HTTP, DB | Utiliser `Http::fake` en test | Token, 429, import partiel. |
| `app/Policies/` | Autorisations | `NewsPolicy`, `PartnerPolicy`, `MediaFilePolicy`, etc. | `AppServiceProvider`, controllers | Modifier permission par permission | Peut exposer admin modules. |
| `app/Http/Middleware/` | Filtres requetes | `EnsureAdminAccess`, `EnsurePublicAccountAccess`, `SetPublicLocale`, `TrackVisitorAnalytics` | Bootstrap middleware aliases | Tests auth obligatoires | Boucles redirect ou tracking non voulu. |
| `bootstrap/` | Boot Laravel | `app.php` | Routes/middleware/exceptions | Rarement | Mauvais redirect auth casse login. |
| `config/` | Configuration | `app.php`, `services.php`, `database.php`, `filesystems.php`, `tournament.php` | `.env`, services | Modifier noms de cles sans secrets | Ne jamais imprimer valeurs `.env`. |
| `database/` | Schema/seeders/factories | migrations, seeders, `sql` | DB, tests | Documentation seulement dans cette phase | Migrations/seeders interdits ici. |
| `database/migrations/` | Definition tables | fichiers `2026_*` | MySQL/sqlite tests | Pas de migration dans cette phase | Changement schema exige audit complet. |
| `database/seeders/` | Donnees demo | Role/Permission/DemoAccess/Showcase | Tests/demo | Ne pas lancer ici | Peut modifier DB live. |
| `public/` | Assets publics | `assets`, `storage` link | Browser | Ne pas toucher ici | Assets publics visibles. |
| `resources/` | Sources frontend | views/css/js | Vite/Blade | Views seulement si demande | Risque UI regression. |
| `resources/views/admin/` | Vues back-office | modules admin | Controllers admin | Modifier module precis | Fake links/form CSRF/RBAC. |
| `resources/views/public/` | Vues publiques | home, pages, partials | Controllers site | Tests publics | Risque overlap/mobile. |
| `routes/` | Declaration routes | `public.php`, `admin.php`, `web.php`, `console.php` | Controllers/middleware | Ajouter route avec tests | Route count/regression. |
| `routes/public.php` | Routes site public | `/`, `/news`, `/matches`, `/account` | Site controllers | Attention ordre routes | Slug conflicts. |
| `routes/console.php` | Closure Artisan | `inspire` | CLI | Commandes dediees plutot dans `app/Console/Commands` | N/A. |
| `storage/` | Logs/cache/uploads privees | `app`, `framework`, `logs` | filesystem | Ne pas versionner | Peut contenir caches/tokens/logs. |
| `tests/` | QA | Feature/Unit suites | PHPUnit | Ajouter tests avant refactor | Full suite peut etre longue. |
| `docs/` | Documentation projet | rapports et runbooks | Aucun runtime | OK pour docs | Ne pas melanger avec app code. |

`routes/public.php` existe. `routes/api.php`: Not found in current codebase.
