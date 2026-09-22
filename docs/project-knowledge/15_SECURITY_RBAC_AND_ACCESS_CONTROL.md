# Securite, RBAC et controle d'acces

## Auth flow

- Login public/staff partage: `/login`, controller `Site\Auth\PublicAuthenticatedSessionController`.
- Redirection post-auth dans `bootstrap/app.php`:
  - staff admin -> `/admin/dashboard`;
  - public account -> `/account`;
  - fallback -> `/`.

## Separation public/staff

- `users.user_type` distingue `public` et `staff`.
- `EnsureAdminAccess` protege les routes admin.
- `EnsurePublicAccountAccess` protege les pages `/account`.

## RBAC

- Tables: `roles`, `permissions`, `role_user`, `permission_role`.
- Policies enregistrees dans `AppServiceProvider`.
- `Gate::before`: role `super-admin` bypass global.
- Permissions: `news.manage`, `news.review`, `news.publish`, `matches.manage`, `partners.manage`, etc.

## Exemples interdits

- Public user -> `/admin/dashboard`: forbidden.
- Journalist -> publish news: forbidden.
- Sponsor Manager -> news: forbidden.
- Media Manager -> audit/settings/news: forbidden selon tests.

## Token handling

- `EXTERNAL_FOOTBALL_API_TOKEN` dans `.env`.
- `FootballDataClient::configStatus()` expose seulement presence/longueur, pas valeur.
- Tests verifient absence de token dans UI.

## `.env`

- `.env` non tracke (`git ls-files .env` vide).
- Ne jamais imprimer ni committer.

## Upload validation

- Partner logo: `image`, `mimes:jpg,jpeg,png,webp`, `max:2048`.
- News media: cover/gallery images `jpg,jpeg,png,webp`, max 4096, gallery max 8.
- Media files: validation dans `StoreMediaFileRequest`.
- Suppression limitee a prefixes attendus (`partners/logos`, `news/covers`, `news/gallery`).

## Audit logs

- `AdminController::recordAudit` et `AuditLogger` alimentent `audit_logs`.
- Audit routes read-only: `admin.audit-logs.index/show`.

## Limitations

- Pas de commande rollback football-data trouvee.
- Les settings locaux sont a 0 au moment de l'audit.
- Les logs/storage peuvent contenir informations sensibles: ne pas committer `storage/`.
