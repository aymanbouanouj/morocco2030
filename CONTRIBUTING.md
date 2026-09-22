# Contributing

## Development Setup

1. Install PHP 8.2+, Composer, Node.js/npm, and MySQL or MariaDB.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and generate a new local key with `php artisan key:generate`.
4. Configure a local database and run `php artisan migrate`.
5. Run `npm install` and `npm run build`.

Never reuse production credentials or commit `.env`.

## Change Discipline

- Keep changes focused and preserve existing authorization boundaries.
- Add or update tests for behavior changes.
- Fake all external HTTP traffic in tests.
- Do not include real users, newsletter addresses, logs, sessions, database dumps, private uploads, or unlicensed media.
- Do not add third-party trademarks or imagery without documented redistribution rights.

## Verification

Before proposing a change, run the relevant focused tests and then the isolated suite:

```bash
php artisan test
npm run build
php artisan route:list --except-vendor
```

The default PHPUnit configuration uses SQLite in memory and publication-local runtime storage. Confirm `.env` remains untracked with `git ls-files .env`.

## Pull Requests

Describe the problem, the implementation, verification performed, security or privacy impact, and any asset provenance. Keep unrelated formatting and refactoring out of the change.
