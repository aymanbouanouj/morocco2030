# Contributing to Morocco2030

Thank you for improving Morocco2030. Contributions should be focused, testable, rights-safe, and consistent with the project's authorization and privacy boundaries.

## Before you start

1. Search existing issues and pull requests.
2. Open an issue for significant behavior, schema, security, dependency, or interface changes.
3. Keep security vulnerabilities private; follow `SECURITY.md` instead of opening a public issue.
4. Do not submit real credentials, user data, provider payloads, private uploads, logs, database dumps, or assets without documented redistribution rights.

## Branch workflow

- Branch from the current `main` branch.
- Use a short descriptive branch name such as `fix/standings-order` or `feature/editorial-filter`.
- Keep commits coherent and avoid unrelated formatting or generated runtime files.
- Rebase or merge the latest `main` before requesting final review when necessary.

## Coding standards

- Follow existing Laravel, PHP, Blade, CSS, and JavaScript conventions.
- Keep authorization in middleware, policies, gates, and tested permission contracts.
- Validate input through form requests or equivalent Laravel validation.
- Escape untrusted output and preserve CSRF protection on state-changing web routes.
- Fake every external HTTP request in automated tests. Real football-data.org calls are prohibited in the test suite.
- Keep `.env`, `vendor/`, `node_modules/`, build output, logs, sessions, local databases, and private uploads untracked.

PHP formatting can be checked with Laravel Pint when a change touches PHP formatting:

```bash
vendor/bin/pint --test
```

## Verification

Install exactly from the lockfiles and run the release checks:

```bash
composer validate --strict
composer install
npm ci
npm run build
php artisan test
php artisan route:list --except-vendor
```

The default PHPUnit configuration uses SQLite in memory, array-backed application services, a synthetic test key, and blocked stray HTTP requests. Do not point tests at a local or production MySQL database.

## Pull requests

Every pull request should include:

- the problem and intended behavior;
- the implementation scope;
- tests added or updated;
- commands run and their results;
- security, privacy, migration, and asset-provenance impact;
- screenshots for meaningful interface changes, using synthetic data only.

Reviewers may ask for narrower scope, stronger authorization coverage, or evidence that external assets can be redistributed.

## Developer Certificate of Origin

Morocco2030 uses the [Developer Certificate of Origin 1.1](https://developercertificate.org/) rather than a custom copyright-assignment agreement. By adding a `Signed-off-by` trailer, you certify that you have the right to submit the contribution under this project's license.

Sign each commit with:

```bash
git commit -s -m "Describe the change"
```

The trailer must use your real contribution identity:

```text
Signed-off-by: Your Name <your-public-email@example.com>
```

## Attribution and licensing

Contributions are licensed under `AGPL-3.0-only`. Contributors retain attribution for their work through Git history and related project records. Do not remove existing copyright, attribution, license, third-party, or modification notices.
