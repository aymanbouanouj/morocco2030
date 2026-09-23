# Stack technique et versions

> **Historical development snapshot:** This document records information observed during the original development environment and may contain versions or local-state details that differ from the current Public Repository Edition. For current publication versions, build status, tests and release metadata, use the root `README.md` and `release/RELEASE-MANIFEST.json`.

## Versions locales

| Element | Version/source |
|---|---|
| PHP CLI | `8.2.12` |
| Composer | `2.8.11` |
| Laravel | `12.56.0` |
| Node | `v24.14.1` |
| npm | `11.11.0` |
| Base locale | MySQL, driver `mysql` selon `php artisan about` |
| Application URL | `127.0.0.1:8010` |
| Environnement | `local`, debug active |

## Composer direct packages

| Package | Version auditee |
|---|---|
| `laravel/framework` | `12.56.0` |
| `laravel/tinker` | `2.11.1` |
| `fakerphp/faker` | `1.24.1` |
| `laravel/pail` | `1.2.6` |
| `laravel/pint` | `1.29.0` |
| `laravel/sail` | `1.56.0` |
| `mockery/mockery` | `1.6.12` |
| `nunomaduro/collision` | `8.9.3` |
| `phpunit/phpunit` | `11.5.55` |

## NPM packages declares

`npm list --depth=0` a retourne `UNMET DEPENDENCY`, donc les packages ne sont pas installes ou `node_modules` est incomplet localement. Versions source: `package.json`.

| Package | Contrainte |
|---|---|
| `vite` | `^7.0.7` |
| `laravel-vite-plugin` | `^2.0.0` |
| `tailwindcss` | `^4.0.0` |
| `@tailwindcss/vite` | `^4.0.0` |
| `axios` | `^1.11.0` |
| `concurrently` | `^9.0.1` |

## Frontend

- Build: Vite via `vite.config.js`.
- Inputs: `resources/css/app.css`, `resources/js/app.js`.
- CSS: Tailwind CSS 4 plugin declare.
- Templates: Laravel Blade sous `resources/views/admin` et `resources/views/public`.
- Assets statiques: `public/assets/...`.

## Testing

- PHPUnit via `phpunit/phpunit`.
- `phpunit.xml`: `APP_ENV=testing`, DB sqlite `:memory:`, queue `sync`, session/cache array.
- Commande: `php artisan test`.

## API provider

- Provider: `football-data`.
- Base URL par defaut: `https://api.football-data.org/v4`.
- Config: `config/services.php` -> `external_football`.
- Token: `EXTERNAL_FOOTBALL_API_TOKEN` dans `.env`; ne jamais afficher.

## Stockage/media

- Laravel filesystem public disk.
- `php artisan about`: `public/storage` LINKED.
- Logos partenaires: `partners/logos`.
- News covers: `news/covers`.
- News gallery: `news/gallery`.

## Auth/RBAC

- Auth Laravel session.
- Users publics et staff separent via `users.user_type`.
- Middleware: `EnsureAdminAccess`, `EnsurePublicAccountAccess`.
- RBAC custom: tables `roles`, `permissions`, `role_user`, `permission_role`, policies, `Gate::before` pour super-admin.

## Avertissements secrets

- `.env` ne doit pas etre versionne.
- Ne pas imprimer `config('services.external_football.token')`.
- Ne pas documenter passwords/hashes.
- Les rapports doivent mentionner les noms de cles, jamais les valeurs secretes.
