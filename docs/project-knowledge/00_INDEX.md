# Morocco 2030 - Index du livre technique

Ce dossier documente le projet Laravel Morocco2030 a partir du code, des routes, des migrations, des fichiers de configuration, des tests et des commandes Artisan. Les donnees locales observees pendant le developpement sont historiques et ne font pas partie de la publication.

## Fichiers principaux

| Fichier | Role |
|---|---|
| `01_EXECUTIVE_SUMMARY.md` | Resume executif du systeme. |
| `02_CONTROL_GUIDE_WHERE_TO_CHANGE_EVERYTHING.md` | Guide pratique: ou changer chaque module, compteur, image, route, vue et workflow. |
| `03_API_REFRESH_RUNBOOK.md` | Runbook de rafraichissement football-data.org. |
| `04_TECH_STACK_AND_VERSIONS.md` | Versions exactes et technologies. |
| `05_SYSTEM_ARCHITECTURE_MVC.md` | Architecture MVC Laravel et flux de requetes. |
| `06_DIRECTORY_STRUCTURE_GUIDE.md` | Lecture dossier par dossier du projet. |
| `07_ROLES_AND_USER_JOURNEYS.md` | Parcours par role et permissions. |
| `08_PAGE_AND_BUTTON_CATALOG.md` | Catalogue pages, routes, boutons, formulaires. |
| `09_DATABASE_SCHEMA_AND_MLD.md` | Schema logique, tables, colonnes majeures et MLD. |
| `10_UML_USE_CASES.md` | Cas d'utilisation et PlantUML. |
| `11_UML_CLASS_DIAGRAM.md` | Classes principales et PlantUML. |
| `12_UML_SEQUENCE_DIAGRAMS.md` | Scenarios de sequence et PlantUML. |
| `13_MODULE_BY_MODULE_TECHNICAL_REPORT.md` | Rapport technique par module. |
| `14_TESTING_AND_QA_REPORT.md` | Tests et assurance qualite. |
| `15_SECURITY_RBAC_AND_ACCESS_CONTROL.md` | Securite, RBAC, secrets, uploads. |
| `16_FINAL_MASTER_REPORT.md` | Rapport final complet pour reference technique/PFE. |

## Diagrammes

| Fichier | Format |
|---|---|
| `diagrams/use_cases.puml` | PlantUML use cases. |
| `diagrams/class_diagram.puml` | PlantUML classes. |
| `diagrams/sequence_public_match_refresh.puml` | Sequence refresh match API. |
| `diagrams/sequence_news_publishing.puml` | Sequence publication news. |
| `diagrams/sequence_partner_logo_upload.puml` | Sequence logo partenaire. |
| `diagrams/sequence_public_account.puml` | Sequence compte public. |
| `diagrams/mld.mmd` | Mermaid ER/MLD. |

## Commandes utiles

```bash
php artisan route:list --except-vendor
php artisan test
php artisan football-data:reconcile-matches
```

Ne jamais imprimer `.env`, les tokens API, les hash de mots de passe ou les secrets.
