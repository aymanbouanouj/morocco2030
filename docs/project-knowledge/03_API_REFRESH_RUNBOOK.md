# Runbook API football-data.org

## Fournisseur

- Provider: `football-data.org`.
- Client: `app/Services/ExternalFootball/FootballDataClient.php`.
- Config: `config/services.php`, section `external_football`.

## Cles de configuration

Ne jamais imprimer les valeurs de `.env`. Les cles attendues sont:

```text
EXTERNAL_FOOTBALL_PROVIDER
EXTERNAL_FOOTBALL_BASE_URL
EXTERNAL_FOOTBALL_API_TOKEN
EXTERNAL_FOOTBALL_TIMEOUT
```

Verification sure:

```bash
php artisan tinker --execute="dump(app(App\Services\ExternalFootball\FootballDataClient::class)->configStatus());"
```

Cette sortie indique si token/base URL sont configures et la longueur du token, sans afficher le token.

## Rafraichir les matches et resultats actuels

Commande trouvee dans le code:

```bash
php artisan football-data:reconcile-matches
```

Classe: `app/Console/Commands/ReconcileFootballDataMatches.php`.

Service appele: `FootballDataWorldCupReconciliationService`.

Effet:

- appelle `/competitions/WC/matches`;
- exige 104 matches source;
- reconcile uniquement les records `matches.code` commencant par `FD-WC-`;
- met a jour `status`, `match_date`, `home_score`, `away_score`, `meta->football_data.status`, `meta->football_data.score_full_time`;
- efface les scores des matches planifies/postponed/cancelled;
- preserve `stadium_id` et `city_id` marocains existants;
- ne lance pas l'import squads.

Verification apres refresh:

```bash
php artisan tinker --execute="dump(['fd_wc_matches'=>App\Models\MatchFixture::where('code','like','FD-WC-%')->count(),'completed'=>App\Models\MatchFixture::where('code','like','FD-WC-%')->where('status','completed')->count(),'scheduled'=>App\Models\MatchFixture::where('code','like','FD-WC-%')->where('status','scheduled')->count()]);"
php artisan test --filter=FootballDataMatchRefresh
php artisan test --filter=FootballData
```

## Rafraichir structure groupes/stages

Action admin trouvee:

- Route: `admin.football-data-import.reconcile-structure`.
- Controller: `Admin\FootballDataImportController@reconcileStructure`.
- Service: `FootballDataWorldCupStructureService`.
- Methode: POST depuis `/admin/football-data-import` avec confirmation `RECONCILE FOOTBALL STRUCTURE`.

Commande CLI dediee structure: `Command not found in current codebase`.

## Reconciliation equipes

Action admin trouvee:

- Route: `admin.football-data-import.reconcile-teams`.
- Controller: `Admin\FootballDataImportController@reconcileTeams`.
- Service: `FootballDataWorldCupTeamReconciliationService`.

Commande CLI dediee teams: `Command not found in current codebase`.

## Squad collection et import

Commandes trouvees:

```bash
php artisan football-data:collect-squads
php artisan football-data:import-squads-from-cache
```

Classes:

- `app/Console/Commands/CollectFootballDataSquads.php`
- `app/Console/Commands/ImportFootballDataSquadsFromCache.php`
- `FootballDataSquadPayloadCollector`
- `FootballDataSquadImportService`

Fonctionnement source-based:

- `collect-squads` collecte les payloads API et gere les limites/retries.
- cache prive: `storage/app/football-data/wc_squads_payloads.json` selon `FootballDataSquadPayloadCollector::CACHE_PATH`.
- `import-squads-from-cache` importe depuis cache complet.
- Les tests verifient que le token n'est pas stocke/expose et que les echecs partiels n'ecrivent pas un cache incomplet.

## Que ne pas lancer sans besoin explicite

Ne pas lancer:

```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan db:wipe
php artisan db:seed
php artisan football-data:collect-squads
php artisan football-data:import-squads-from-cache
```

sauf si une phase specifique le demande et que le risque 429/import est accepte.

## Detecter HTTP 429

Le client retourne:

- `ok=false`
- `status=429`
- `error` safe
- `rate_limit.retry_after`, `requests_available_minute`, `request_counter_reset`

Que faire:

1. Ne pas relancer en boucle.
2. Attendre `Retry-After` si fourni.
3. Garder les donnees locales intactes.
4. Relancer les tests fake HTTP, pas l'API reelle.

## Verifier pages publiques

```bash
php artisan test --filter=FootballDataPublicConsistency
php artisan test --filter=FootballDataMatchRefreshPublic
```

Pages a ouvrir:

- `/matches`
- `/results`
- `/standings`
- `/`

## Rollback

Rollback DB automatique des refreshs n'est pas implemente comme commande dediee. Option sure trouvee: utiliser backup MySQL manuel avant une operation risquee. `Not implemented`: commande de rollback football-data.
