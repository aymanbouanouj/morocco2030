# Architecture MVC Laravel

## Principe general

Laravel structure ce projet autour du flux:

```text
Browser -> Route -> Middleware -> Controller -> Service/Support -> Model -> Database -> Blade View
```

## Flux public

Exemple `/matches`:

1. Route: `routes/public.php`, `Route::get('/matches', [MatchController::class, 'index'])->name('matches.index')`.
2. Middleware: `public.locale`, `track.analytics`.
3. Controller: `App\Http\Controllers\Site\MatchController@index`.
4. Model: `App\Models\MatchFixture`.
5. Source de scope: si FD-WC existe, `SiteController::fdWorldCupMatchesQuery()`.
6. Vue: `resources/views/public/matches/index.blade.php`.

## Flux admin

Exemple `/admin/news/create`:

1. Route: `routes/admin.php`, resource `news`.
2. Middleware: `auth`, `admin`.
3. Middleware admin: `EnsureAdminAccess` verifie compte staff actif et autorise.
4. Controller: `Admin\NewsController@create`.
5. Model: `NewsCategory::active()`.
6. Vue: `resources/views/admin/news/create.blade.php`, partial `_form.blade.php`.

## Auth/RBAC

- Login partage: `Site\Auth\PublicAuthenticatedSessionController`.
- Apres login, `bootstrap/app.php` redirige:
  - admin capable -> `admin.dashboard`;
  - public account -> `account.index`;
  - sinon `home`.
- Policies enregistrees dans `AppServiceProvider`.
- `Gate::before` donne l'acces global au role `super-admin`.

## Integration football-data

Refresh CLI:

```text
Command football-data:reconcile-matches
 -> FootballDataWorldCupReconciliationService
 -> FootballDataClient::getWorldCupMatches()
 -> football-data.org /competitions/WC/matches
 -> MatchFixture/Team updates
 -> public pages read updated DB
```

Admin UI:

`/admin/football-data-import` -> `FootballDataImportController` -> services dry-run/import/reconcile.

## News publishing

```text
Journalist -> Admin\NewsController@store -> News(status=draft)
Journalist -> NewsWorkflowController@submitForReview -> status=pending_review
Chief Editor/Super Admin -> approve -> status=approved
Chief Editor/Super Admin -> publish -> status=published, published_at set
Public /news -> SiteController::publishedPublicNewsQuery()
```

## Partner logo upload

```text
Sponsor Manager -> admin.partners.store/update
 -> Admin\PartnerController::partnerDataWithLogo()
 -> Storage::disk('public')->store('partners/logos')
 -> partners.logo_path
 -> Partner::logoUrl()
 -> public partners/home
```

## Public account

```text
Public User -> /login -> PublicAuthenticatedSessionController
 -> /account -> Site\AccountController
 -> User favorites/notifications/profile/settings
 -> resources/views/public/account/*
```

## Blade layout

- Public layout: `resources/views/public/layouts/app.blade.php`.
- Public header/footer: `resources/views/public/partials/header.blade.php`, `footer.blade.php`.
- Admin layout: `resources/views/admin/layouts/app.blade.php`.
- Admin sidebar/topbar: `resources/views/admin/partials/sidebar.blade.php`, `topbar.blade.php`.

## Storage flow

- Config: `config/filesystems.php`.
- Public URL via `Storage::disk('public')->url(...)`.
- `public/storage` is linked according to `php artisan about`.

## Testing flow

- Feature/unit tests under `tests/Feature` and `tests/Unit`.
- Test DB: sqlite in-memory from `phpunit.xml`.
- External API in tests must use `Http::fake`.
