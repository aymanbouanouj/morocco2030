# Rapport final master - Morocco 2030

## Vue d'ensemble

Morocco 2030 est une application Laravel 12 combinant portail public et back-office RBAC. Elle presente competition, matches, resultats, classements, equipes, joueurs, villes, stades, partenaires, news, compte public, recherche et carte. Le back-office controle les donnees operationnelles, l'editorial, les medias, les roles et l'integration football-data.org.

## Objectif

Donner a l'utilisateur un controle complet:

- comprendre d'ou viennent les chiffres;
- savoir ou changer chaque module;
- rafraichir les donnees API;
- publier des news;
- gerer partenaires/logos;
- verifier avant demo.

## Scope fonctionnel

Public:

- home, news, matches, results, standings, knockout, teams, players, cities, stadiums, partners, map, search, account.

Admin:

- dashboard, users/roles, news/categories/workflow, matches/events/statistics/lineups, teams/players, groups, cities/stadiums, partners/logos, media-files/readiness, translations/languages, audit logs, settings, football-data import/reconcile.

## Stack

- PHP 8.2.12.
- Laravel 12.56.0.
- MySQL/MariaDB en execution; SQLite en memoire pour les tests.
- Blade + Vite + Tailwind declare.
- PHPUnit 11.5.55.
- football-data.org API via Laravel HTTP client.

## Architecture

MVC Laravel:

```text
Browser -> routes -> middleware -> controller -> service -> model -> DB -> Blade
```

Les modules critiques utilisent des services:

- `ExternalFootball/*` pour API.
- `Sports/*` pour standings/knockout.
- `NewsWorkflowManager` pour workflow editorial.
- `NewsMediaStorage` pour media news.
- `PublicSearchService` pour recherche.

## Database

Tables principales: `users`, `roles`, `permissions`, `news`, `news_categories`, `matches`, `teams`, `players`, `groups`, `standings`, `cities`, `stadiums`, `partners`, `media_files`, `audit_logs`, `settings`.

Les donnees football-data sont marquees par:

- `matches.code` en `FD-WC-*`;
- `matches.meta`;
- `teams.meta->source`;
- `players.external_provider/external_id`;
- `teams.coach_name` pour coaches.

## Roles

Roles demo audites: Super Admin, Platform Admin, Chief Editor, Journalist, Match Manager, Team Manager, Venue Manager, Sponsor Manager, Media Manager, Translator, Analyst, Support Agent, Public User.

Le RBAC est strict et couvert par tests.

## Workflows essentiels

### Refresh API

```bash
php artisan football-data:reconcile-matches
```

Verifie et met a jour uniquement les matches `FD-WC-*`.

### Publication news

1. Garder une categorie active (`Tournament Updates`).
2. Journalist cree draft.
3. Journalist submit review.
4. Chief Editor/Super Admin approve/publish.
5. Public `/news` et homepage affichent uniquement published public.

### Partner logo

1. Sponsor Manager ouvre `/admin/partners`.
2. Cree/modifie partner.
3. Upload logo valide.
4. `partners.logo_path` stocke chemin public.
5. `/partners` et home affichent logo si active.

## Media/uploads

Public disk lie. Uploads valides par request classes. Les chemins sont controles par prefixes avant suppression.

## Tests

Les resultats historiques doivent etre revalides dans l'environnement isole de publication.

Avant demo:

```bash
php artisan view:clear
php artisan view:cache
php artisan test --filter=FootballData
php artisan test --filter=News
php artisan test --filter=SponsorManagerPartners
php artisan test --filter=MediaReview
php artisan test --filter=PublicAccountPage
php artisan test
php artisan route:list --except-vendor
```

## Securite

- `.env` non tracke.
- Token API non affiche.
- Admin via auth + middleware + policies.
- Public user bloque de l'admin.
- Audit logs pour actions admin.

## Limitations

- `/bracket` non implemente; utiliser `/knockout`.
- Pas de rollback API dedie trouve.
- NPM dependencies non installees localement selon `npm list`.

## Checklist demo

1. `php artisan about`.
2. `php artisan migrate:status`.
3. Ne lancer `php artisan football-data:reconcile-matches` qu'avec un environnement et un fournisseur explicitement configures.
4. Tester `/`, `/matches`, `/results`, `/standings`, `/news`, `/partners`.
5. Login staff: verifier dashboard role.
6. Publier une news test si besoin.
7. Verifier `.env` non tracke.
