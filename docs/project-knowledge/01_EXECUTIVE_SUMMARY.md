# Resume executif

## Identification du projet

- Nom applicatif: `MOROCCO 2030`.
- Framework: Laravel `12.56.0`.
- Branche publique: `main` apres preparation de publication.
- Nombre de routes applicatives: a verifier avec `php artisan route:list --json --except-vendor`.
- Etat des tests: les resultats historiques ne remplacent pas une execution propre dans l'environnement de publication.

## Objectif fonctionnel

Le projet est un portail public et un back-office d'administration pour une plateforme de demonstration Morocco 2030. Il couvre:

- Pages publiques: accueil, news, matches, resultats, standings, equipes, joueurs, villes, stades, carte, partenaires, recherche, compte public.
- Back-office: dashboard, utilisateurs, roles, news, categories, competition, villes/stades, partenaires, medias, traductions, audit logs, settings, football-data.
- Integration football-data.org pour les donnees Coupe du Monde: equipes, groupes, matches, scores, statuts, squads.
- Workflow editorial: journaliste -> brouillon -> revue -> approbation/publication -> site public.
- RBAC strict par roles operationnels.

## Architecture synthetique

Laravel applique MVC:

```text
Browser -> routes/*.php -> middleware -> controller -> service/support -> model -> MySQL -> Blade view
```

Exemple public:

`GET /matches` -> `routes/public.php` -> `Site\MatchController@index` -> `MatchFixture` -> `resources/views/public/matches/index.blade.php`.

Exemple admin:

`GET /admin/news/create` -> `routes/admin.php` -> `auth` + `admin` middleware -> `Admin\NewsController@create` -> `NewsCategory::active()` -> `resources/views/admin/news/create.blade.php`.

## Points de controle majeurs

- Ne pas hardcoder les compteurs: ils viennent de requetes Eloquent dans `HomeController`, `DashboardController` et `SiteController`.
- Refresh matches: `php artisan football-data:reconcile-matches`.
- Publication news: categorie active obligatoire, draft par journaliste, publication par `news.publish`.
- Logo partenaire: upload admin via `PartnerController`, stockage `partners.logo_path`, rendu public via `Partner::logoUrl()`.
- Acces admin: middleware `EnsureAdminAccess`, policies et permissions.

## Limites connues

- `npm list --depth=0` indique des dependances NPM non installees localement (`UNMET DEPENDENCY`). Les versions front-end documentees proviennent donc de `package.json`.
- Les commandes football-data dependront toujours des limites fournisseur, notamment HTTP 429.
- `/bracket` n'existe pas comme route; la route actuelle est `/knockout`.
