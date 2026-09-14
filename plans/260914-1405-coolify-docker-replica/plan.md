# Coolify Docker + DB/Image Replica Tooling

Status: in progress | Branch: main

## Outcome
1. Dockerize the Laravel 12 app (PHP 8.2, no Vite/npm, Tailwind via CDN) for
   Coolify deployment. Use `expose` (not `ports`) everywhere so the container
   never publishes a host port, avoiding collisions with other projects on the
   same Coolify server. Coolify's built-in proxy (Traefik) routes to the app
   via its internal network using the FQDN configured in the Coolify UI.
2. Artisan commands to export/import DB data + local images (`storage/app/public`)
   as a single portable archive, to replicate quickly between local and the
   Coolify server. Import merges: rows matching an existing primary key are
   overwritten with the imported version; rows/files that exist only locally
   are kept (no deletes). Files are overwritten by relative path; local-only
   files are kept.

## Constraints / decisions (confirmed with user)
- MariaDB runs as a service inside this project's own `docker-compose.yml`
  (self-contained, own named volume) — not an external Coolify DB resource.
- No Redis — app has no jobs and uses the `database` driver for
  session/cache/queue; skip Redis to keep the stack minimal.
- No Vite/npm build stage (repo convention: Tailwind CDN + vanilla JS only).
- Single app container (Apache + mod_php) — no queue worker/scheduler
  container, since `app/Jobs` is empty and `routes/console.php` has no
  scheduled tasks.

## Acceptance criteria
- `docker compose build` succeeds locally; `docker compose up` serves the app,
  migrates DB, and static images resolve via `/storage/...`.
- `docker-compose.yml` uses `expose:` only (no `ports:`) for both `app` and
  `mariadb` services.
- `php artisan replica:export` produces a single zip with DB table JSON +
  images + manifest.
- `php artisan replica:import <zip>` auto-backs up current data first (dev
  rule: back up before bulk data change), then upserts DB rows by primary key
  and overwrite-merges images, without deleting local-only rows/files.
- Feature test covers export → mutate local data → import merge behavior.
- `docs/deployment.md` documents the Coolify setup steps and the replica
  commands.

## Non-goals
- No external/managed Coolify DB resource wiring (per decision above).
- No Redis, no queue worker container.
- No UI for import/export (CLI only, matches "artisan command" scope).
