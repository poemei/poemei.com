# Bootstrap (`/app/bootstrap.php`) — (2.0.7)

Bootstrap initializes core and derives runtime choices (notably **active theme**).

## Responsibilities

- Load core services from `/app/core/*`
- Load config + connect DB (via `db` core)
- Load settings
- Resolve **active theme**
- Trigger SEO generator (`seo::run($site_theme)`)
- Include social sharing helpers (`/app/lib/share.php`)

## Theme Resolution (DB + Filesystem Fallback)

Bootstrap resolves `$site_theme` using this order:

1. Default: `default`
2. If DB exists:
   - `settings.name = 'site_theme'` value wins when non-empty
   - else first enabled theme from `themes.enabled=1`
3. Sanitize theme slug to `[a-z0-9_-]`
4. **Filesystem truth check**:
   - if `/public/themes/<slug>` is missing, fall back to `default`

**No DB writes are performed** during theme resolution. DB is preference; filesystem is reality.

## SEO Hook

Bootstrap calls:

- `seo::run($site_theme);`

This is responsible for maintaining:
- `sitemap.xml`
- `ror.xml` (as per core comments)

