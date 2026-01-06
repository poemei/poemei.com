# Updater (`/app/update`) — (2.0.7)

Updater is a core component used to fetch and apply new core packages.

## Files

- `/app/update/lib.php` — updater library (download, verify, lock, maintenance, apply)
- `/app/update/run.php` — runner entry
- `/app/update/update.json` — config (manifest URL, channel, etc.)
- `/app/update/packages/` — downloaded tarballs
- `/app/update/stage/` — extraction/working dir
- `/app/update/backup/` — backups (best-effort)
- `/app/update/logs/` — updater logs

## Remote Version Source

Core uses the version domain:

- `https://version.chaoscms.org/db/version.json`

Packages are downloaded from the same host (e.g. `chaos-core-<ver>.tar.gz`) and validated via sha256.

## Safety Sequence

Typical run:

1. Download package
2. Verify sha256
3. Turn LOCK on (`/app/data/update.lock`)
4. Turn MAINTENANCE on (`/app/data/maintenance.flag`)
5. Backup current core (best-effort)
6. Apply package
7. MAINTENANCE off
8. LOCK off

If apply fails, maintenance and lock are turned off and the error is logged.

