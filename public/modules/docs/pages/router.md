# Router (`/app/router.php`) — (2.0.7)

Router dispatches requests by path segments.

## Maintenance & Update Gate

Router checks two files:

- `/app/data/update.lock`
- `/app/data/maintenance.flag`

If either exists, router loads the maintenance module:

- `/app/modules/maintenance/main.php`

Exception:
- requests to `/admin` are allowed through during maintenance/update.

## Core Route Rules

Router defines core modules that are always available:

### Home
Paths:
- `/` or `/home`

Resolution:
- checks `/public/modules/home/main.php`
- else `/app/modules/home/main.php`

(first existing file is required)

### Posts
Path:
- `/posts`

Loads:
- `/app/modules/posts/main.php`

### Pages
Path:
- `/pages`

Loads:
- `/app/modules/pages/main.php`

### Media
Path:
- `/media`

Loads:
- `/app/modules/media/main.php`

### Account
Path:
- `/account`

Loads:
- `/app/modules/account/main.php` (and prints a friendly error if missing)

## Plugins

Router includes plugin entry files when enabled slugs are present.
Plugins are sanitized to `[a-z0-9_-]` before inclusion.

