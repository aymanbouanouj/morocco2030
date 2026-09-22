# Schema base de donnees et MLD

Source: migrations, models, inspection information_schema MySQL en lecture seule.

## Tables principales

| Table | Sens metier | Colonnes majeures | Relations principales |
|---|---|---|---|
| `users` | Comptes publics/staff | `id`, `name`, `email`, `phone`, `user_type`, `status`, `preferred_locale`, `password`, timestamps, `deleted_at` | roles via `role_user`, favorites, notifications, news author/editor |
| `roles` | Roles RBAC | `name`, `slug`, `is_system` | permissions via `permission_role`, users via `role_user` |
| `permissions` | Permissions granulaires | `module`, `slug`, `name` | roles via pivot |
| `role_user` | Pivot user-role | `user_id`, `role_id` | users/roles |
| `permission_role` | Pivot role-permission | `role_id`, `permission_id` | roles/permissions |
| `news` | Articles editoriaux | `category_id`, `author_id`, `editor_id`, `title`, `slug`, `status`, `visibility`, `published_at`, `cover_image_path`, `gallery_image_paths`, `video_url`, `meta`, `deleted_at` | category, author, editor, workflows, media |
| `news_categories` | Taxonomie news | `parent_id`, `name`, `slug`, `status`, `sort_order`, `deleted_at` | parent/children/news |
| `editorial_workflows` | Historique workflow | `workflowable_type/id`, `submitted_by`, `reviewed_by`, `status`, `current_step`, `published_at`, `payload` | morph to News |
| `teams` | Equipes | `group_id`, `name`, `code`, `slug`, `coach_name`, `team_type`, `status`, `meta`, `deleted_at` | group, players, matches |
| `players` | Joueurs | `team_id`, names, `slug`, `shirt_number`, `position`, `nationality_code`, `external_provider`, `external_id`, `meta`, `deleted_at` | team |
| `matches` | Fixtures | `stadium_id`, `city_id`, `group_id`, `home_team_id`, `away_team_id`, `code`, `slug`, `stage_type`, `match_date`, `status`, scores, `published_at`, `meta`, `deleted_at` | teams, group, city, stadium, events/statistics/lineups/progressions |
| `groups` | Groupes competition | `name`, `code`, `sort_order` | teams, matches, standings |
| `standings` | Classement persiste | `group_id`, `team_id`, `position`, stats, `points`, `meta` | group/team |
| `cities` | Villes hotes | `name`, `slug`, `code`, `country_code`, `region`, coordinates, `status`, `deleted_at` | stadiums, matches |
| `stadiums` | Stades | `city_id`, `name`, `slug`, `capacity`, coordinates, `surface_type`, `status`, `deleted_at` | city, matches |
| `partners` | Partenaires/sponsors | `name`, `slug`, `category`, `tier`, `website_url`, `logo_path`, `logo_alt`, `status`, `deleted_at` | media/translations |
| `media_files` | Media uploades | `disk`, `path`, `mime_type`, `size`, `visibility`, `status`, metadata, `deleted_at` | polymorphic media_relations |
| `media_relations` | Liaison media-entite | `media_file_id`, `mediable_type/id`, `collection`, `sort_order` | MediaFile + entite |
| `audit_logs` | Trace admin | `user_id`, `auditable_type/id`, `action`, `old_values`, `new_values`, `route_name`, `occurred_at` | user + morph |
| `settings` | Parametres | `key`, `value`, `type`, `group`, flags | Settings admin |
| `user_favorites` | Favoris publics | `user_id`, `favorable_type/id` | User + morph |
| `user_notifications` | Notifications public/support | `user_id`, `type`, `title`, `body`, `read_at`, `meta` | User |
| `visitor_analytics` | Tracking public | `user_id`, `session_id`, `path`, `route_name`, `language_code`, `device_type`, `event_at`, `meta` | dashboard analytics |

## Colonnes JSON/meta importantes

- `matches.meta`: provenance football-data, group/stage API, score full-time.
- `teams.meta`: source football-data, placeholder flag, crest.
- `players.meta`: provenance payload squad.
- `news.meta`, `gallery_image_paths`: informations editoriales/media.
- `audit_logs.old_values/new_values`: snapshots JSON.
- `settings.value`: valeur typable.

## Soft deletes

SoftDeletes detectes sur plusieurs models/tables: `users`, `news`, `news_categories`, `teams`, `players`, `cities`, `stadiums`, `partners`, `media_files` et autres tables avec `deleted_at`.

## Provenance football-data

- Matches source: `matches.code` format `FD-WC-{id}` et `matches.meta`.
- Equipes: `teams.meta->source = football-data.org`, `meta->football_data`.
- Joueurs: colonnes `external_provider`, `external_id` ajoutees par migration `2026_06_15_000100_add_external_provenance_to_players_table`.
- Coaches: stockes comme texte dans `teams.coach_name`; modele `Coach` non trouve.

## Diagramme

Voir `diagrams/mld.mmd`.
