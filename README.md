# Morocco2030

Morocco2030 is a Laravel demonstration platform for presenting and operating a football tournament concept. It combines a multilingual public portal with a role-based administration area for editorial content, fixtures, teams, venues, partners, media review, and tournament operations.

This is an independent educational and portfolio project. It is not affiliated with, endorsed by, or sponsored by FIFA, the 2030 FIFA World Cup, any football federation, or any commercial partner.

## Capabilities

- Public tournament pages for fixtures, results, standings, knockout rounds, teams, players, host cities, stadiums, news, partners, search, and user accounts.
- Role-based administration for users, permissions, editorial workflows, sports data, venues, translations, media, settings, and audit records.
- Optional football-data.org integration for tournament data, with preview and reconciliation workflows.
- PHPUnit coverage for public routes, access control, editorial operations, partner visibility, media handling, and external-data workflows.

## Technology

- PHP 8.2+
- Laravel 12
- Blade, Vite, and Tailwind CSS 4
- MySQL or MariaDB for application use
- SQLite in memory for the default test suite

## Local Setup

Prerequisites: PHP 8.2+, Composer, Node.js/npm, and MySQL or MariaDB.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set local database credentials in `.env`, then run:

```bash
php artisan migrate
npm install
npm run build
php artisan serve
```

No demo credentials are published. Create local administrative access through the project's supported local setup workflow and never commit credentials.

## Testing

The default PHPUnit configuration isolates database, cache, session, mail, queue, and filesystem state from an application installation.

```bash
php artisan test
```

External football-data tests must use Laravel HTTP fakes. Do not place live provider credentials in test configuration.

## Production Requirements

Use a production-specific `.env` with `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, secure cookies, a unique `APP_KEY`, and dedicated service credentials. Never deploy `.env`, runtime logs, sessions, test artifacts, local databases, or private uploads from this repository.

## Documentation

- [Technical documentation](docs/project-knowledge/00_INDEX.md)
- [Publication provenance](docs/PUBLICATION_PROVENANCE.md)
- [Security policy](SECURITY.md)
- [Contributing guide](CONTRIBUTING.md)
- [Third-party assets](THIRD_PARTY_ASSETS.md)

## Licensing

No project-wide open-source license has been selected. Until the copyright holder publishes an explicit license, the project code is all rights reserved and redistribution is not granted. Third-party components and assets remain governed by their own licenses; see `NOTICE` and `THIRD_PARTY_ASSETS.md`.
