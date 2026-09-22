# Rapport tests et QA

## Framework

- PHPUnit `11.5.55`.
- Laravel test runner: `php artisan test`.
- Config: `phpunit.xml`.
- DB test: sqlite `:memory:`.
- Queue: `sync`.
- Cache/session: array.

## Etat de publication

Les chiffres historiques de la suite ne sont pas presentes comme une garantie de publication. Reexecuter la suite dans l'environnement isole avant toute diffusion.

## Commandes

```bash
php artisan test
php artisan test --filter=FootballData
php artisan test --filter=News
php artisan test --filter=SponsorManagerPartners
php artisan test --filter=MediaReview
php artisan test --filter=PublicAccountPage
```

## Couverture par domaine

| Domaine | Tests |
|---|---|
| football-data | `FootballData*Test`, `FootballDataSquadPayloadCollectorTest`, public consistency tests |
| Partners/logos | `SponsorManagerPartnersTest`, `PublicPartnerLogoDisplayTest`, `PublicPartnersVisibilityTest` |
| Media review | `MediaReadinessAdminModuleTest`, `MediaReviewContextualAssetsTest`, media file workflow tests |
| Public account | `PublicAccountPageTest`, `PublicUserAccountAccessTest`, `PublicStaffAuthSplitTest` |
| News | `AdminNews*`, `NewsPublishingWorkflowTest`, `PublicNewsVisibilityTest` |
| RBAC | `StrictRoleVisibilityMatrixTest`, `AdminDirectRouteAuthorizationContractTest`, `AdminAccessControlContractTest` |
| Public pages | `PublicFrontendPagesTest`, `PublicLayoutOverlapTest`, multilingual/search/map tests |

## API testing

Les tests football-data utilisent `Http::fake`; ils ne doivent pas appeler le vrai fournisseur.

## Isolation obligatoire

- `phpunit.xml` force SQLite en memoire, cache/session/mail en memoire et une racine de fichiers de test sous `storage/framework/testing`.
- Les tests football-data utilisent `Http::fake`; aucun appel fournisseur reel ne doit etre autorise.
- Ne jamais lancer les tests de publication depuis une copie reliee a la base ou au stockage d'une installation de demonstration.

## Performance

Pour iteration rapide, utiliser les filtres par module avant la suite complete.

## Troubleshooting

- Echec auth/RBAC: verifier role seeders, policies, `EnsureAdminAccess`.
- Echec football-data: verifier `Http::fake`, confirmations typed, count 104.
- Echec views: `php artisan view:clear && php artisan view:cache`.
- Echec NPM: `npm list` indique dependances non installees; faire `npm install` seulement hors phase docs si necessaire.
