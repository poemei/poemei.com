# Core Architecture (2.0.7)

Chaos CMS core is everything under:

- `/app` (server-side core runtime)
- `/public` (web root: themes + assets + entrypoint)

The guiding principles are:

- **KISS**: minimal indirection, explicit logic
- **Filesystem is truth**: DB stores preference; missing folders fall back safely
- **Core is not public**: core modules live in `/app/modules`
- **Updates are first-class**: maintenance + lock + updater

## Request Flow

1. Web entrypoint (`/public/index.php`) sets up `$docroot`, request path parts, and includes core.
2. `/app/bootstrap.php` initializes core services and resolves active theme.
3. `/app/router.php` routes requests to:
   - maintenance module (if flag/lock)
   - admin
   - core modules (pages/posts/media)
   - home module fallback (public vs app)
   - plugins/modules as applicable

## Canonical Directories

- `/app/core/` — DB/auth/version/seo/modules/plugins/utility
- `/app/admin/` — admin router + wrapper + views
- `/app/modules/` — core modules (not web-exposed)
- `/app/update/` — updater engine
- `/app/data/` — runtime state (`version.json`, `maintenance.flag`, `update.lock`, caches)

- `/public/themes/` — themes (must include `default`)
- `/public/assets/` — CSS/JS assets (admin.css is explicitly allowed public)
- `/public/modules/` — public modules (e.g. `home`)

